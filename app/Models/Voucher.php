<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    // Table name (because Laravel would default to "vouchers")
    protected $table = 'voucher';

    // Primary key
    protected $primaryKey = 'voucherID';

    // Primary key is string, not auto-increment
    public $incrementing = false;
    protected $keyType = 'string';

    // If you want Laravel to manage timestamps
    public $timestamps = true;

    // Map Laravel's timestamps to your column names
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    // Mass assignable fields
    protected $fillable = [
        'voucherID',
        'discountAmount',
        'minSpend',
        'expiryDate',
        'usageLimit',
    ];

    // Optional: Cast types for convenience
    protected $casts = [
        'discountAmount' => 'decimal:2',
        'minSpend'       => 'decimal:2',
        'expiryDate'     => 'date',
        'usageLimit'     => 'integer',
    ];
}
