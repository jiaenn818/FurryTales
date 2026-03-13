<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'PaymentID';

    protected $fillable = [
        'PurchaseID',
        'PaymentMethod',
        'Amount',
        'TransactionID',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'PurchaseID', 'PurchaseID');
    }
}
