<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Accessory;
use App\Models\Outlet;
use Illuminate\Support\Facades\Auth;
use App\Models\SearchHistory;

class AccessoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Accessory::query();

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
                $query->where(function ($q) use ($keyword) {
                    $q->where('AccessoryName', 'LIKE', "%{$keyword}%")
                      ->orWhere('Brand', 'LIKE', "%{$keyword}%")
                      ->orWhere('Category', 'LIKE', "%{$keyword}%");
                });
            }
        }

        /* =========================
        🎯 FILTERS
        ========================== */
        if ($request->filled('category')) {
            $query->where('Category', $request->category);
        }
        if ($request->filled('outlet')) {
            $query->join('outlet_accessories', 'accessories.AccessoryID', '=', 'outlet_accessories.AccessoryID')
                  ->join('outlet', 'outlet_accessories.OutletID', '=', 'outlet.OutletID')
                  ->where('outlet.City', $request->outlet)
                  ->select('accessories.*')
                  ->distinct();
        }
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('Price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('Price', '<=', $request->max_price);
            });
        }

        /* =========================
        📦 FETCH ACCESSORIES
        ========================== */
        $accessories = $query->with('variants')->paginate(9);

        /* =========================
        🧠 SEARCH HISTORY (UI)
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
        $categories = Accessory::select('Category')->distinct()->orderBy('Category')->get();
        $brands = Accessory::select('Brand')->distinct()->orderBy('Brand')->get();
        $outlets = Outlet::select('City')->distinct()->orderBy('City')->get();
        $dbMinPrice = \App\Models\AccessoryVariant::min('Price') ?: 0;
        $dbMaxPrice = \App\Models\AccessoryVariant::max('Price') ?: 9999;

        return view('client.accessory.index', compact(
            'accessories',
            'searchHistories',
            'categories',
            'brands',
            'outlets',
            'dbMinPrice',
            'dbMaxPrice'
        ));
    }

    public function show($id)
    {
        $accessory = Accessory::with(['supplier', 'outlets', 'variants' => function ($query) {
                                  $query->orderBy('VariantID', 'asc')->with('outlets.outlet');
                              }])
                              ->where('AccessoryID', $id)
                              ->firstOrFail();

        // Parse Variants into a structured options array
        // Example VariantKey: "Color:Red|Size:M"
        $details = [];
        foreach ($accessory->variants as $variant) {
            $parts = explode('|', $variant->VariantKey);
            foreach ($parts as $part) {
                if (str_contains($part, ':')) {
                    [$label, $value] = explode(':', $part, 2);
                    $label = trim($label);
                    $value = trim($value);
                    
                    if (!isset($details[$label])) {
                        $details[$label] = [];
                    }
                    if (!in_array($value, $details[$label])) {
                        $details[$label][] = $value;
                    }
                }
            }
        }

        return view('client.accessory.show', compact('accessory', 'details'));
    }
}
