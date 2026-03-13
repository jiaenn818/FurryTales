<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_items';
    protected $primaryKey = 'PurchaseItemID';

    protected $fillable = [
        'PurchaseID',
        'ItemID',
        'AccessoryID',
        'OutletID',
        'VariantID',
        'Quantity',
        'Price'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'PurchaseID', 'PurchaseID');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'ItemID', 'PetID');
    }

    public function accessory()
    {
        return $this->belongsTo(Accessory::class, 'AccessoryID', 'AccessoryID');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'OutletID', 'OutletID');
    }

    public function variant()
    {
        return $this->belongsTo(AccessoryVariant::class, 'VariantID', 'VariantID');
    }
}
