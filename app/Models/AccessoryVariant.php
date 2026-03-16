<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessoryVariant extends Model
{
    protected $table = 'accessory_variants';
    protected $primaryKey = 'VariantID';
    public $incrementing = true; // Bigint auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'AccessoryID',
        'VariantKey',
        'Price',
    ];

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'AccessoryID', 'AccessoryID');
    }

    public function outlets()
    {
        return $this->hasMany(OutletAccessory::class, 'VariantID', 'VariantID');
    }

        public function outletStocks()
    {
        return $this->hasMany(OutletAccessory::class, 'VariantID', 'VariantID');
    }
}
