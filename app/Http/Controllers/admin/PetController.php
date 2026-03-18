<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Pet;
use App\Models\Outlet;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    // Display all pets
    public function index()
    {
        // Check auth
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        // 1️⃣ For stats: all pets (ignore filters)
        $allPets = Pet::all();
        $petCount = $allPets->count();

        // 2️⃣ Build query for table with search/filter
        $query = Pet::with(['outlet', 'supplier'])->orderBy('PetID', 'desc');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('PetName', 'like', "%{$search}%")
                    ->orWhere('Breed', 'like', "%{$search}%")
                    ->orWhere('Type', 'like', "%{$search}%")
                    ->orWhere('PetID', 'like', "%{$search}%");
            });
        }

        if ($category = request('category')) {
            $query->where('Type', $category);
        }

        $pets = $query->paginate(10)->withQueryString();

        // 3️⃣ Categories for stats & filters
        $categories = $this->getAllCategories();

        return view('admin.pets.petList', compact('pets', 'allPets', 'categories', 'petCount'));
    }

    public function add()
    {
        $categories = Pet::getAllCategories();
        $outlets = Outlet::all();
        $suppliers = Supplier::all();

        // ✅ Generate next PetID
        $nextPetID = Pet::generateNextPetID('A');

        return view('admin.pets.petAdd', compact(
            'categories',
            'outlets',
            'suppliers',
            'nextPetID'
        ));
    }


    // Handle the form submission
    public function store(Request $request)
    {
        $request->validate([
            'petName' => 'required|string|max:255',
            'type' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',

            'healthStatus' => 'required|string',
            'vaccinationStatus' => 'required|string',
            'outletID' => 'required|string',
            'supplierID' => 'required|string',
            'size' => 'required|string',
            'gender' => 'required|string',

            'image' => 'nullable|image|max:5120',
            'extraImage1' => 'nullable|image|max:5120',
            'extraImage2' => 'nullable|image|max:5120',
            'extraImage3' => 'nullable|image|max:5120',
            'extraImage4' => 'nullable|image|max:5120',
        ]);

        $pet = new Pet();
        $pet->PetID = Pet::generateNextPetID('A');
        $pet->PetName = $request->petName;
        $pet->Type = $request->type;
        $pet->Breed = $request->breed;
        $pet->Color = $request->color;
        $pet->Age = $request->age;
        $pet->Price = $request->price;
        $pet->HealthStatus = $request->healthStatus;
        $pet->VaccinationStatus = $request->vaccinationStatus;
        $pet->Description = $request->description;
        $pet->Size = $request->size;
        $pet->Gender = $request->gender;
        $pet->OutletID = $request->outletID;
        $pet->SupplierID = $request->supplierID;

        // ====== HANDLE MAIN + EXTRA IMAGES ======
        $allImages = array_merge(
            ['image'], // main image
            ['extraImage1', 'extraImage2', 'extraImage3', 'extraImage4'] // extra images
        );

        foreach ($allImages as $index => $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $extension = $file->getClientOriginalExtension();
                $filename = $pet->PetID . '-' . ($index + 1) . '.' . $extension;
                $path = $file->storeAs('pets', $filename, 'uploads');

                // Assign to the correct column in database
                $pet->{'ImageURL' . ($index + 1)} = 'image/' . $path;
            } else {
                $pet->{'ImageURL' . ($index + 1)} = null;
            }
        }

        $pet->save();

        // Save image features if provided
        if ($request->has('image_features')) {
            $features = json_decode($request->image_features, true);
            if (is_array($features)) {
                $pet->update(['image_features' => $features]);
            }
        }


        return redirect()->route('admin.pets.index')->with('message', 'Pet added successfully!')->with('message_type', 'success');
    }

    // Show form to edit pet
    public function edit(Pet $pet)
    {
        $outlets = Outlet::all();
        $suppliers = Supplier::all();
        $categories = $this->getAllCategories(); // function that returns all pet categories

        return view('admin.pets.petEdit', compact('pet', 'outlets', 'suppliers', 'categories'));
    }

    // Update pet
    public function update(Request $request, Pet $pet)
    {
        // Validate request
        $request->validate([
            'petName' => 'required|string|max:255',
            'type' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'extraImages.*' => 'nullable|image|max:2048',
        ]);

        // ===== MAIN IMAGE (ImageURL1) =====
        if ($request->hasFile('image')) {
            $pet->ImageURL1 = $this->storeImageRandom($request->file('image'), $pet->ImageURL1);
        }

        // ===== HANDLE EXTRA IMAGES WITH SHIFTING =====
        $uploadedFiles = $request->file('extraImages', []);
        $oldImageData = $request->input('extraImagesOld', []);

        // Get current images from database into an array
        $currentImages = [
            0 => $pet->ImageURL2,
            1 => $pet->ImageURL3,
            2 => $pet->ImageURL4,
            3 => $pet->ImageURL5
        ];

        // Track which old images to keep (not delete)
        $imagesToKeep = [];
        $imagesToDelete = [];

        // First pass: identify what to keep and what to delete
        foreach ($oldImageData as $index => $oldValue) {
            $currentImage = $currentImages[$index] ?? null;

            if ($oldValue === "DELETE_ME") {
                // Mark for deletion
                if ($currentImage) {
                    $imagesToDelete[] = $currentImage;
                }
            } elseif (!empty($oldValue) && $oldValue !== "DELETE_ME") {
                // This is an existing image that should be kept (and shifted)
                $imagesToKeep[] = $oldValue;
            }
            // Empty strings mean new images or empty slots
        }

        // Delete ONLY the images marked for deletion
        foreach ($imagesToDelete as $imagePath) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Now process the final images array with shifting
        $finalImages = [];
        $newIndex = 0;

        foreach ($oldImageData as $index => $oldValue) {
            if ($oldValue === "DELETE_ME") {
                // Skip deleted images
                continue;
            }

            if (isset($uploadedFiles[$index]) && $uploadedFiles[$index]->isValid()) {
                // New file uploaded
                // Check if there's an old image at this position that needs deleting
                $oldFileToDelete = $currentImages[$index] ?? null;
                if ($oldFileToDelete && !in_array($oldFileToDelete, $imagesToKeep)) {
                    // This old file is being replaced
                    if (Storage::disk('public')->exists($oldFileToDelete)) {
                        Storage::disk('public')->delete($oldFileToDelete);
                    }
                }

                $finalImages[$newIndex] = $this->storeImageRandom($uploadedFiles[$index], null);
                $newIndex++;
            } elseif (!empty($oldValue) && $oldValue !== "DELETE_ME") {
                // Existing image, keep it (it will be shifted)
                $finalImages[$newIndex] = $oldValue;
                $newIndex++;
            } else {
                // Empty slot
                $finalImages[$newIndex] = null;
                $newIndex++;
            }

            // Stop if we've reached max
            if ($newIndex >= 4) break;
        }

        // Fill remaining slots with null
        while ($newIndex < 4) {
            $finalImages[$newIndex] = null;
            $newIndex++;
        }

        // Update database columns
        $pet->ImageURL2 = $finalImages[0] ?? null;
        $pet->ImageURL3 = $finalImages[1] ?? null;
        $pet->ImageURL4 = $finalImages[2] ?? null;
        $pet->ImageURL5 = $finalImages[3] ?? null;

        // ===== UPDATE OTHER FIELDS =====
        $pet->PetName = $request->petName;
        $pet->Type = $request->type;
        $pet->Breed = $request->breed;
        $pet->Color = $request->color;
        $pet->Age = $request->age;
        $pet->Price = $request->price;
        $pet->HealthStatus = $request->healthStatus;
        $pet->VaccinationStatus = $request->vaccinationStatus;
        $pet->OutletID = $request->outletID;
        $pet->SupplierID = $request->supplierID;
        $pet->Size = $request->size;
        $pet->Gender = $request->gender;
        $pet->Description = $request->description;

        // Save to database
        $pet->save();

        // Save image features if provided
        if ($request->has('image_features')) {
            $features = json_decode($request->image_features, true);
            if (is_array($features)) {
                $pet->update(['image_features' => $features]);
            }
        }


        return redirect()->route('admin.pets.index')
            ->with('message', 'Pet updated successfully!')
            ->with('message_type', 'success');
    }
    /**
     * Store an image with a random filename and delete old image
     */
    private function storeImageRandom($file, $oldFilename = null)
    {
        // Delete old file if exists
        if ($oldFilename) {
            $cleanPath = str_replace('image/', '', $oldFilename);
            if (Storage::disk('uploads')->exists($cleanPath)) {
                Storage::disk('uploads')->delete($cleanPath);
            }
        }

        // Generate random filename
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;

        // Store the new file
        $path = $file->storeAs('pets', $filename, 'uploads');

        return 'image/' . $path; // Returns 'image/pets/filename.jpg'
    }

    // Delete pet
    public function destroy(Pet $pet)
    {
        $pet->delete();

        return redirect()->route('admin.pets.index')
            ->with('success', "Pet '{$pet->PetName}' deleted successfully!");
    }

    public function detectBreed(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        try {
            $tempPath = $request->file('image')->store('detect', 'uploads');
            $fullPath = public_path('image/' . $tempPath);

            $result = Pet::detectBreed($fullPath);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'breed' => '',
                'type'  => '',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ---------------- Category ----------------

    private function getAllCategories()
    {
        return \DB::table('pet_categories')
            ->orderBy('category_name')
            ->get();
    }

    public function addCategory(Request $request)
    {
        // Validate request
        $request->validate([
            'category_name' => 'required|string|unique:pet_categories,category_name',
        ]);

        \DB::table('pet_categories')->insert([
            'category_name' => $request->category_name
        ]);

        return redirect()->back()->with('success', 'Category added successfully!');
    }
}
