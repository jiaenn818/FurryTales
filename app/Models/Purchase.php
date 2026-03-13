<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    use HasFactory;

    protected $primaryKey = 'PurchaseID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PurchaseID',
        'CustomerID',
        'OrderDate',
        'Method',
        'TotalAmount',
        'DeliveryAddress',
        'Postcode',
        'State',
        'Time',
        'Status',
        'VoucherID',
        'DiscountAmount',
        'DeliveredDate',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'customerID');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'PurchaseID', 'PurchaseID');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'PurchaseID', 'PurchaseID');
    }

    public function orderRating()
    {
        return $this->hasOne(OrderRating::class, 'PurchaseID', 'PurchaseID');
    }

    public static function getStatusEnum()
    {
        $result = \DB::select("SHOW COLUMNS FROM purchases WHERE Field = 'Status'");

        if (!empty($result)) {
            $type = $result[0]->Type; // e.g., enum('Pending','Picked Up','Out for Delivery')
            preg_match("/^enum\('(.*)'\)$/", $type, $matches);
            if (isset($matches[1])) {
                return explode("','", $matches[1]);
            }
        }

        return [];
    }
}