<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $table = 'pet';
    protected $primaryKey = 'PetID';   // 🟢 important
    public $incrementing = false;      // because PetID = P001
    protected $keyType = 'string';     // because PetID is NOT integer
    public $timestamps = false; // disable if you don't have created_at/updated_at

    protected $fillable = [
        'PetID',
        'OutletID',
        'SupplierID',
        'PetName',
        'Type',
        'Breed',
        'Age',
        'Gender',
        'Color',
        'Size',
        'Price',
        'HealthStatus',
        'VaccinationStatus',
        'Description',
        'ImageURL1',
        'ImageURL2',
        'ImageURL3',
        'ImageURL4',
        'ImageURL5',
        'image_features',
    ];

    protected $casts = [
        'image_features' => 'array',
    ];

    public function purchaseItem()
    {
        return $this->hasOne(PurchaseItem::class, 'ItemID', 'PetID');
    }

    public function isSold()
    {
        return $this->purchaseItem()->exists();
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'OutletID', 'OutletID');
    }

        public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'PetID', 'PetID');
    }

    // ==================== CRUD & UTILITY METHODS ====================

    // Generate next PetID
    public static function generateNextPetID($outletCode = 'A')
    {
        $lastPet = self::where('PetID', 'like', $outletCode . '%')
            ->orderBy('PetID', 'desc')
            ->first();
        if ($lastPet) {
            $number = intval(substr($lastPet->PetID, 1)) + 1;
            return $outletCode . str_pad($number, 4, '0', STR_PAD_LEFT);
        }
        return $outletCode . '0001';
    }

    // Validate attributes
    public function validate()
    {
        $errors = [];

        if (!$this->PetID) $errors[] = "Pet ID is required";
        if (!$this->PetName) $errors[] = "Pet name is required";
        if (!$this->Type) $errors[] = "Pet type is required";
        if (!$this->Breed) $errors[] = "Breed is required";
        if ($this->Age <= 0) $errors[] = "Age must be positive";
        if ($this->Price <= 0) $errors[] = "Price must be positive";
        if (!$this->HealthStatus) $errors[] = "Health status is required";
        if (!$this->VaccinationStatus) $errors[] = "Vaccination status is required";

        return $errors;
    }

    // Convert model to array (optional)
    public function toArray()
    {
        return parent::toArray();
    }

    // Handle image uploads
    public static function handleImageUploads($files, $petID)
    {
        $images = [];
        $fields = array_merge(['image'], ['extraImage1', 'extraImage2', 'extraImage3', 'extraImage4']);

        foreach ($fields as $i => $field) {
            if (isset($files[$field]) && $files[$field]->isValid()) {
                $ext = $files[$field]->getClientOriginalExtension();
                $filename = $petID . '-' . ($i + 1) . '.' . $ext;
                $path = $files[$field]->storeAs('public/pets', $filename);
                $images[] = Storage::url($path);
            } else {
                $images[] = null;
            }
        }

        return $images;
    }

    // ==================== CATEGORY METHODS ====================

    public static function addCategory($categoryName)
    {
        $categoryName = trim($categoryName);

        if (self::categoryExists($categoryName)) {
            return false;
        }

        return \DB::table('pet_categories')->insert([
            'category_name' => $categoryName,
        ]);
    }

    public static function getAllCategories()
    {
        return \DB::table('pet_categories')->select('category_name')->orderBy('category_name')->get();
    }

    // ==================== BREED DETECTION ====================
    public static function detectBreed($imagePath)
    {
        $script = base_path('ai/detect_breed.py');
        $image  = realpath($imagePath);

        if (!$image || !file_exists($script)) {
            return ['breed' => '', 'type' => '', 'error' => 'File not found'];
        }

        $command = "py " . escapeshellarg($script) . " " . escapeshellarg($image) . " 2>&1";
        $output = trim(shell_exec($command));

        if (strpos($output, '|') !== false) {
            [$breed, $type] = explode('|', $output, 2);
            return [
                'breed' => trim($breed),
                'type'  => trim($type),
            ];
        }

        return ['breed' => '', 'type' => '', 'error' => $output];
    }
}
