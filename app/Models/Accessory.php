<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $table = 'accessories';
    protected $primaryKey = 'AccessoryID';
    public $incrementing = false; // AccessoryID is a string
    protected $keyType = 'string';

    protected $fillable = [
        'AccessoryID',
        'SupplierID',
        'AccessoryName',
        'Category',
        'Description',
        'Brand',
        'ImageURL1',
        'ImageURL2',
        'ImageURL3',
        'ImageURL4',
        'ImageURL5',
    ];

    protected $casts = [
        'Image' => 'string',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'SupplierID', 'SupplierID');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_accessories', 'AccessoryID', 'OutletID')
                    ->withPivot('StockQty', 'VariantID')
                    ->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(AccessoryVariant::class, 'AccessoryID', 'AccessoryID');
    }

    public function outletAccessories()
    {
        return $this->hasMany(OutletAccessory::class, 'AccessoryID', 'AccessoryID');
    }

    public function getMinPriceAttribute()
    {
        return $this->variants->min('Price');
    }

    public static function generateNextAccessoryID($prefix = 'A')
    {
        $lastAccessory = self::where('AccessoryID', 'like', $prefix . '%')
            ->orderBy('AccessoryID', 'desc')
            ->first();

        if ($lastAccessory) {
            // Extract numeric part after prefix
            $number = intval(substr($lastAccessory->AccessoryID, strlen($prefix))) + 1;
            return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        }

        // First ID
        return $prefix . '001';
    }     

    public static function getCategoryOptions()
    {
        $column = \DB::select("SHOW COLUMNS FROM `accessories` WHERE Field = ?", ['Category']);
        if (!empty($column)) {
            $type = $column[0]->Type; // This should be a string like "enum('Feeding','Grooming & Hygiene','Health & Safety')"
            preg_match("/^enum\((.*)\)$/", $type, $matches);
            if (isset($matches[1])) {
                return array_map(function ($val) {
                    return trim($val, "'");
                }, explode(",", $matches[1]));
            }
        }
        return [];
    }

    
    public function findOutletStockByVariantID($variantID)
    {
        return OutletAccessory::with('outlet') // eager load outlet info
            ->where('VariantID', $variantID)
            ->get();
    }
}
