<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $primaryKey = 'customerID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customerID',
        'userID',
        'address',
        'profile_photo',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'CustomerID', 'CustomerID');
    }
}
