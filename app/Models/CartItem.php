<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $primaryKey = 'CartItemID';
    protected $fillable = ['CartID', 'PetID', 'AccessoryID', 'OutletID', 'VariantID', 'Quantity'];

    protected $casts = [
        'SelectedDetails' => 'array',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'PetID', 'PetID');
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
