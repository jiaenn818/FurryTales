<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $primaryKey = 'CartID';
    protected $fillable = ['CustomerID'];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'CartID', 'CartID');
    }
}