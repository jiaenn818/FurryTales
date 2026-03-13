<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'AppointmentID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'CustomerID',
        'PetID',
        'AppointmentDateTime',
        'Method',
        'CustomerName',
        'CustomerPhone',
        'Status',
    ];

    protected $casts = [
        'AppointmentDateTime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'customerID');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'OutletID', 'OutletID');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'PetID', 'PetID');
    }
}