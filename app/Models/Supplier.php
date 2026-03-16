<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'SupplierID';
    public $incrementing = false; // because SupplierID is not auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'SupplierID',
        'SupplierName',
        'SupplierEmail',
        'SupplierPhoneNumber'
    ];

    // Example: relation to pets
    public function pets()
    {
        return $this->hasMany(Pet::class, 'SupplierID', 'SupplierID');
    }

    public function accessories()
    {
        return $this->hasMany(Accessory::class, 'SupplierID', 'SupplierID');
    }


    // Generate next SupplierID
    public static function generateNextSupplierID()
    {
        $last = self::orderBy('SupplierID', 'desc')->first();
        if (!$last) {
            return 'SUP001';
        }
        $num = (int) substr($last->SupplierID, 3) + 1;
        return 'SUP' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}
