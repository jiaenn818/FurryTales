<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutletAccessory extends Model
{
    protected $table = 'outlet_accessories';
    protected $primaryKey = 'OutletAccessoryID';
    public $incrementing = true; // Bigint auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'OutletID',
        'AccessoryID',
        'VariantID',
        'StockQty',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'OutletID', 'OutletID');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'AccessoryID', 'AccessoryID');
    }

    public function variant()
    {
        return $this->belongsTo(AccessoryVariant::class, 'VariantID', 'VariantID');
    }
}
