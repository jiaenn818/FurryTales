<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $primaryKey = 'userID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'userID',
        'name',
        'email',
        'phoneNo',
        'password',
        'status',
        'user_type',
        'login_attempts',
        'lockout_until',
    ];

    protected $hidden = ['password'];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

        public function isStaff(): bool
    {
        return $this->staff !== null;
    }

    public function isRider(): bool
    {
        return $this->rider !== null;
    }

    public function isManager(): bool
    {
        return $this->isStaff() && strtolower($this->staff->Role) === 'manager';
    }

    /* ───────── RELATIONSHIPS ───────── */

    // Customer child (1–1)
    public function customer()
    {
        return $this->hasOne(Customer::class, 'userID', 'userID');
    }

    // Staff child (1–1)
    public function staff()
    {
        return $this->hasOne(Staff::class, 'UserID', 'userID');
    }

    public function rider()
    {
        return $this->hasOne(Rider::class, 'userID', 'userID');
    }
}
