<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRating extends Model
{
    use HasFactory;

    protected $table = 'order_ratings';
    protected $primaryKey = 'id';

    protected $fillable = ['PurchaseID','rating','review'];

    // Relation: The order this rating belongs to
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'PurchaseID', 'PurchaseID');
    }

}
