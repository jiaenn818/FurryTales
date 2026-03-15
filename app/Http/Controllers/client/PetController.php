<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use App\Models\BrowsingHistory;
use App\Models\SearchHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $query = Pet::query();

        /* =========================
        🔍 SEARCH
        ========================== */
        if ($request->has('search')) {
            $keyword = trim($request->search);

            if ($keyword !== '') {
                if (Auth::check() && Auth::user()->customer) {
                    $customerID = Auth::user()->customer->customerID;

                    SearchHistory::updateOrCreate(
                        ['CustomerID' => $customerID, 'keyword' => $keyword],
                        ['searched_at' => now()]
                    );
                }

                $allTerms = Pet::select('PetName', 'Breed', 'Type')
                    ->distinct()
                    ->get()
                    ->flatMap(fn($pet) => [$pet->PetName, $pet->Breed, $pet->Type])
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $similarTerms = [];
                $threshold = strlen($keyword) <= 4 ? 1 : 2;

                foreach ($allTerms as $term) {
                    if (levenshtein(strtolower($keyword), strtolower($term)) <= $threshold) {
                        $similarTerms[] = $term;
                        continue;
                    }

                    if (str_contains($term, ' ')) {
                        foreach (explode(' ', $term) as $word) {
                            if (levenshtein(strtolower($keyword), strtolower($word)) <= $threshold) {
                                $similarTerms[] = $term;
                                break;
                            }
                        }
                    }
                }

                $query->where(function ($q) use ($keyword, $similarTerms) {
                    $q->where('PetName', 'LIKE', "%{$keyword}%")
                      ->orWhere('Breed', 'LIKE', "%{$keyword}%")
                      ->orWhere('Type', 'LIKE', "%{$keyword}%");

                    if (!empty($similarTerms)) {
                        $q->orWhereIn('PetName', $similarTerms)
                          ->orWhereIn('Breed', $similarTerms)
                          ->orWhereIn('Type', $similarTerms);
                    }
                });
            }
        }

        /* =========================
        🎯 FILTERS
        ========================== */
        if ($request->filled('type')) {
            $query->where('Type', $request->type)->distinct();
        }
        if ($request->filled('breed')) {
            $query->where('Breed', $request->breed)->distinct();
        }
        if ($request->filled('outlet')) {
            $query->join('outlet', 'pet.OutletID', '=', 'outlet.OutletID')
                  ->where('outlet.City', $request->outlet)
                  ->select('pet.*');
        }
        if ($request->filled('min_price')) {
            $query->where('Price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('Price', '<=', $request->max_price);
        }

        /* =========================
        📦 FETCH PETS
        ========================== */
        $pets = $query->with('purchaseItem')->get();

        /* =========================
        🧠 SEARCH HISTORY
        ========================== */
        $searchHistories = collect();
        if (Auth::check() && Auth::user()->customer) {
            $searchHistories = SearchHistory::where('CustomerID', Auth::user()->customer->customerID)
                ->orderByDesc('searched_at')
                ->limit(5)
                ->get();
        }

        /* =========================
        📊 SIDEBAR DATA
        ========================== */
        $types = Pet::select('Type')->distinct()->orderBy('Type')->get();
        $breeds = Pet::select('Breed', 'Type')->distinct()->orderBy('Breed')->get()->groupBy('Type');
        $outlets = Outlet::select('City')->distinct()->orderBy('City')->get();
        $dbMinPrice = Pet::min('Price');
        $dbMaxPrice = Pet::max('Price');

        /* =========================
        🔀 SORT PETS
        ========================== */
        $pets = $pets->sort(function ($a, $b) use ($request) {
            $aSold = $a->isSold();
            $bSold = $b->isSold();

            if ($aSold !== $bSold) return $aSold ? 1 : -1;

            if ($request->sort_price === 'asc') return $a->Price <=> $b->Price;
            if ($request->sort_price === 'desc') return $b->Price <=> $a->Price;

            return strnatcmp($b->PetID, $a->PetID);
        })->values();

        /* =========================
        🧠 RECOMMENDATIONS
        ========================== */
        $recommendedPets = collect();
        $recommendCount = $request->input('recommend_count', 5);

        if (Auth::check() && Auth::user()->customer) {
            $customerId = Auth::user()->customer->customerID;
            $recommendations = $this->fetchRecommendationsFromPython($customerId, $recommendCount);

            if (!empty($recommendations)) {
                $scores = collect($recommendations)->pluck('final_score', 'PetID');
                $recommendedPets = Pet::whereIn('PetID', $scores->keys())
                    ->with('purchaseItem')->get();

                // Attach scores to the recommended pets
                $recommendedPets->each(function($pet) use ($scores) {
                    $pet->recommendation_score = $scores[$pet->PetID] ?? 0;
                });
            }
        }

        /* =========================
        🧠 SYNC SCORES TO MAIN LIST
        ========================== */
        $pets->each(function($p) use ($recommendedPets) {
            $rec = $recommendedPets->firstWhere('PetID', $p->PetID);
            if ($rec) {
                $p->recommendation_score = $rec->recommendation_score;
            }
        });

        /* =========================
        🧠 DEFAULT VS FILTERED MODE
        ========================== */
        $allPets = $pets;

        $recommendedIds = $recommendedPets->pluck('PetID')->toArray();

        $recommended = $allPets->filter(fn($p) => in_array($p->PetID, $recommendedIds) && !$p->isSold());
        $available = $allPets->filter(fn($p) => !$p->isSold() && !in_array($p->PetID, $recommendedIds));
        $sold = $allPets->filter(fn($p) => $p->isSold());

        $orderedPets = $recommended->concat($available)->concat($sold)->values();

        /* =========================
        📄 PAGINATION
        ========================== */
        $perPage = 9;
        $totalItems = $orderedPets->count();
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentPage = min($currentPage, max(1, ceil($totalItems / $perPage))); // ensure page within range

        $pageItems = $orderedPets->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $pets = new LengthAwarePaginator(
            $pageItems,
            $totalItems,
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        /* =========================
        Return to view
        ========================== */
        $isDefaultBrowse = !$request->filled(['search', 'type', 'breed', 'outlet', 'min_price', 'max_price']);

        return view('client.pets.index', compact(
            'pets',
            'searchHistories',
            'types',
            'breeds',
            'outlets',
            'dbMinPrice',
            'dbMaxPrice',
            'recommendedPets',
            'recommendCount',
            'isDefaultBrowse'
        ));
    }

    public function getRecommendations(Request $request)
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $customerId = Auth::user()->customer->customerID;
        $recommendCount = $request->input('recommend_count', 5);
        
        $recommendations = $this->fetchRecommendationsFromPython($customerId, $recommendCount);

        if (!empty($recommendations)) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No recommendations found']);
    }

    private function fetchRecommendationsFromPython($customerId, $count)
    {
        $pythonScript = base_path('recommender/recommend.py');

        if (!file_exists($pythonScript)) {
            return [];
        }

        $command = "python3 \"$pythonScript\" $customerId $count";
        $output = shell_exec($command . " 2>&1");
        $result = json_decode($output, true);

        return $result['recommendations'] ?? [];
    }

    /* =========================
       OTHER METHODS (UNCHANGED)
       ========================= */

    public function getPetsForImageSearch()
    {
        $pets = Pet::whereDoesntHave('purchaseItem')->get()->map(function ($pet) {
            return [
                'id' => $pet->PetID,
                'name' => $pet->PetName,
                'breed' => $pet->Breed,
                'price' => $pet->Price,
                'images' => array_values(array_filter([
                    $pet->ImageURL1 ? asset($pet->ImageURL1) : null,
                    $pet->ImageURL2 ? asset($pet->ImageURL2) : null,
                    $pet->ImageURL3 ? asset($pet->ImageURL3) : null,
                    $pet->ImageURL4 ? asset($pet->ImageURL4) : null,
                    $pet->ImageURL5 ? asset($pet->ImageURL5) : null,
                ])),
                'url' => route('client.pets.show', $pet->PetID),
                'image_features' => $pet->image_features,
            ];
        });

        return response()->json($pets);
    }

    public function savePetFeatures(Request $request)
    {
        $request->validate([
            'features' => 'required|array',
            'features.*.pet_id' => 'required|string',
            'features.*.features' => 'required|array',
        ]);

        foreach ($request->features as $item) {
            $pet = Pet::find($item['pet_id']);
            if ($pet) {
                $pet->update(['image_features' => $item['features']]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function getPetDetails($id)
    {
        $pet = Pet::find($id);

        if (!$pet) return response()->json(['error' => 'Pet not found'], 404);

        return response()->json([
            'PetID' => $pet->PetID,
            'PetName' => $pet->PetName,
            'Breed' => $pet->Breed,
            'Type' => $pet->Type,
            'Price' => $pet->Price,
            'Photo1' => asset($pet->imageURL1 ?: 'image/default-pet.png'),
            'isSold' => $pet->isSold()
        ]);
    }

    public function show($PetID)
    {
        $pet = Pet::findOrFail($PetID);
        $user = Auth::user();

        if ($user && $user->customer) {
            $customerID = $user->customer->customerID;
            $sessionKey = "viewed_pet_{$customerID}_{$PetID}";

            if (!session()->has($sessionKey)) {
                BrowsingHistory::create([
                    'CustomerID' => $customerID,
                    'PetID' => $PetID,
                    'viewed_at' => now()
                ]);
                session()->put($sessionKey, true);
            }
        }

        return view('Client.pets.show', compact('pet'));
    }

    public function destroySearchHistory($id)
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return response()->json(['success' => false], 403);
        }

        SearchHistory::where('SearchHistoryID', $id)
            ->where('CustomerID', Auth::user()->customer->customerID)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function clearSearchHistory()
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return response()->json(['success' => false], 403);
        }

        SearchHistory::where('CustomerID', Auth::user()->customer->customerID)->delete();

        return response()->json(['success' => true]);
    }
}
