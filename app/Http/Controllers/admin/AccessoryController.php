<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Accessory;
use App\Models\AccessoryVariant;
use App\Models\OutletAccessory;
use App\Models\Supplier;
use Illuminate\Support\Str;


class AccessoryController extends Controller
{
    // Display all accessories
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        $query = Accessory::with(['supplier', 'outletAccessories']);

        // Filter by search term
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('AccessoryName', 'like', "%{$search}%")
                ->orWhere('AccessoryID', 'like', "%{$search}%")
                ->orWhere('Category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->input('category')) {
            $query->where('Category', $category);
        }

        $accessories = $query->orderBy('AccessoryID', 'desc')->paginate(10)->withQueryString();

        $categories = Accessory::select('Category')->distinct()->get();

        return view('admin.accessory.accessoryList', compact('accessories', 'categories'));
    }
    // Show form to add accessory
    public function add()
    {
        $suppliers = Supplier::all();
        $nextID = Accessory::generateNextAccessoryID('A');
        $categoryOptions = Accessory::getCategoryOptions();
        $outlets = \App\Models\Outlet::all();

        return view('admin.accessory.accessoryAdd', [
            'nextAccessoryID' => $nextID,
            'suppliers' => $suppliers,
            'categoryOptions' => $categoryOptions,
            'outlets' => $outlets,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'AccessoryID'   => 'required|unique:accessories,AccessoryID',
                'SupplierID'  => 'required|exists:supplier,SupplierID',
                'AccessoryName' => 'required|string|max:255',
                'Category'      => 'required|string',
                'Brand'         => 'nullable|string|max:255',
                'Description'   => 'nullable|string',
                'variants' => 'required|array|min:1', // At least 1 variant
                'variants.*.outlets' => 'required|array|min:1', // Each variant must have at least 1 outlet
                'variants.*.outlets.*.OutletID' => 'required|exists:outlet,OutletID',
                'variants.*.outlets.*.StockQty' => 'required|integer|min:0',
                'ImageURL1'       => 'nullable|image|max:5120',
                'ImageURL2'       => 'nullable|image|max:5120',
                'ImageURL3'       => 'nullable|image|max:5120',
                'ImageURL4'       => 'nullable|image|max:5120',
                'ImageURL5'       => 'nullable|image|max:5120',
            ],
            [
                'variants.required' => 'Please add at least one variant.',
                'variants.*.outlets.required' => 'Each variant must have at least one outlet stock.',
                'variants.*.outlets.*.OutletID.required' => 'Please select an outlet for the variant.',
                'variants.*.outlets.*.StockQty.required' => 'Please enter stock quantity for the outlet.',
            ],

            [
                'ImageURL1.max' => 'Image 1 is too large. Maximum size is 5MB.',
                'ImageURL2.max' => 'Image 2 is too large. Maximum size is 5MB.',
                'ImageURL3.max' => 'Image 3 is too large. Maximum size is 5MB.',
                'ImageURL4.max' => 'Image 4 is too large. Maximum size is 5MB.',
                'ImageURL5.max' => 'Image 5 is too large. Maximum size is 5MB.',
                'ImageURL1.image' => 'Image 1 must be a valid image file.',
                'ImageURL2.image' => 'Image 2 must be a valid image file.',
                'ImageURL3.image' => 'Image 3 must be a valid image file.',
                'ImageURL4.image' => 'Image 4 must be a valid image file.',
                'ImageURL5.image' => 'Image 5 must be a valid image file.',
            ]
        );


        DB::transaction(function () use ($request) {

            /* ======================
           1️⃣ Save Accessory
        ====================== */
            $accessory = new Accessory();
            $accessory->AccessoryID   = $request->AccessoryID;
            $accessory->SupplierID = $request->SupplierID;
            $accessory->AccessoryName = $request->AccessoryName;
            $accessory->Category      = $request->Category;
            $accessory->Brand         = $request->Brand;
            $accessory->Description   = $request->Description;

            for ($i = 1; $i <= 5; $i++) {
                if ($request->hasFile('ImageURL' . $i)) {
                    $file = $request->file('ImageURL' . $i);

                    if (!$file->isValid()) {
                        return redirect()->back()->withErrors([
                            'ImageURL' . $i => "Image {$i} failed to upload. Maximum allowed size is 5MB."
                        ])->withInput();
                    }

                    $accessory->{'ImageURL' . $i} = $this->storeImageRandom($file);
                }
            }

            $accessory->save();

            /* ======================
           2️⃣ Save Variants
        ====================== */
            foreach ($request->variants ?? [] as $variantData) {

                // Skip empty variant
                if (empty($variantData['VariantKey'])) {
                    continue;
                }

                $variant = new AccessoryVariant();
                $variant->AccessoryID    = $accessory->AccessoryID;
                $variant->VariantKey     = $variantData['VariantKey'];
                $variant->Price= $variantData['Price'] ?? 0;
                $variant->save();

                /* ======================
               3️⃣ Save Outlet Stock
            ====================== */
                foreach ($variantData['outlets'] ?? [] as $outletData) {

                    if (empty($outletData['OutletID'])) {
                        continue;
                    }

                    OutletAccessory::create([
                        'OutletID'    => $outletData['OutletID'],
                        'AccessoryID' => $accessory->AccessoryID,
                        'VariantID'   => $variant->VariantID,
                        'StockQty'    => $outletData['StockQty'] ?? 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.accessories.index')
            ->with('message', 'Accessory added successfully!')
            ->with('message_type', 'success');
    }

    private function storeImageRandom($file, $oldFilename = null)
    {
        // delete old image if exists
        if ($oldFilename) {
            $cleanPath = str_replace('image/', '', $oldFilename);
            if (Storage::disk('uploads')->exists($cleanPath)) {
                Storage::disk('uploads')->delete($cleanPath);
            }
        }

        // generate new filename: timestamp + 6 random letters
        $filename = time() . '_' . Str::upper(Str::random(6)) . '.' . $file->getClientOriginalExtension();

        // store in 'public/image/accessories'
        $path = $file->storeAs('accessories', $filename, 'uploads');

        // return path saved in DB
        return 'image/' . $path;
    }



    // Show edit form
    public function edit(Accessory $accessory)
    {
        $suppliers = Supplier::all();
        $nextID = Accessory::generateNextAccessoryID('A');
        $categoryOptions = Accessory::getCategoryOptions();
        $outlets = \App\Models\Outlet::all();
        $accessory->load('variants');


        return view('admin.accessory.accessoryEdit', [
            'accessory' => $accessory,
            'nextAccessoryID' => $nextID,
            'suppliers' => $suppliers,
            'categoryOptions' => $categoryOptions,
            'outlets' => $outlets,
        ]);
    }



    public function update(Request $request, $accessoryID)
    {
        $accessory = Accessory::findOrFail($accessoryID);

        DB::transaction(function () use ($request, $accessory) {

            // 1. Update accessory info
            $accessory->AccessoryName = $request->AccessoryName;
            $accessory->Category = $request->Category;
            $accessory->SupplierID = $request->SupplierID;
            $accessory->Brand = $request->Brand;
            $accessory->Description = $request->Description;

            // Handle images
            for ($i = 1; $i <= 5; $i++) {
                // Remove image if flagged
                if ($request->input('removeImage' . $i) == '1') {
                    if ($accessory->{'ImageURL' . $i}) {
                        $cleanPath = str_replace('image/', '', $accessory->{'ImageURL' . $i});
                        Storage::disk('uploads')->delete($cleanPath);
                    }
                    $accessory->{'ImageURL' . $i} = null;
                }


                // Save new uploaded file
                if ($request->hasFile('ImageURL' . $i)) {
                    $file = $request->file('ImageURL' . $i);
                    $accessory->{'ImageURL' . $i} = $this->storeImageRandom($file, $accessory->{'ImageURL' . $i});
                }
            }

            $accessory->save();

            // 2. Delete old variants & outlet stocks
            $accessory->variants()->each(function ($variant) {
                $variant->outletStocks()->delete(); // delete stocks first
                $variant->delete(); // then delete variant
            });

            // 3. Insert new variants + outlet stocks
            foreach ($request->variants ?? [] as $vIndex => $variantData) {
                if (empty($variantData['VariantKey'])) continue;

                $variant = new AccessoryVariant();
                $variant->AccessoryID = $accessory->AccessoryID;
                $variant->VariantKey = $variantData['VariantKey'];
                $variant->Price = $variantData['Price'] ?? 0;
                $variant->save();

                foreach ($variantData['outlets'] ?? [] as $outletData) {
                    if (empty($outletData['OutletID'])) continue;

                    OutletAccessory::create([
                        'AccessoryID' => $accessory->AccessoryID,
                        'VariantID' => $variant->VariantID,
                        'OutletID' => $outletData['OutletID'],
                        'StockQty' => $outletData['StockQty'] ?? 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.accessories.index')
            ->with('message', 'Accessory updated successfully!')
            ->with('message_type', 'success');
    }



    // Delete accessory
    public function destroy(Accessory $accessory)
    {
        DB::transaction(function () use ($accessory) {
            // Delete all variants and their outlet stocks
            $accessory->variants->each(function ($variant) {
                $variant->outletStocks()->delete(); // delete outlet_accessories
                $variant->delete(); // delete variants
            });

            // Delete accessory images from storage
            for ($i = 1; $i <= 5; $i++) {
                if ($accessory->{'ImageURL' . $i}) {
                    $cleanPath = str_replace('image/', '', $accessory->{'ImageURL' . $i});
                    Storage::disk('uploads')->delete($cleanPath);
                }
            }


            // Delete accessory itself
            $accessory->delete();
        });

        return redirect()
            ->route('admin.accessories.index')
            ->with('message', 'Accessory deleted successfully!')
            ->with('message_type', 'success');
    }
}
