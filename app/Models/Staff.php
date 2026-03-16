<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Staff extends Model
{
    protected $table = 'staff';

    protected $primaryKey = 'StaffID';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'StaffID',
        'UserID',
        'OutletID',
        'Role',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ───────── RELATIONSHIPS ───────── */
    function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'userID');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'OutletID', 'OutletID');
    }



    public static function getRoles(): array
    {
        $type = DB::select("SHOW COLUMNS FROM staff WHERE Field = 'Role'")[0]->Type;
        preg_match("/^enum\('(.*)'\)$/", $type, $matches);
        return explode("','", $matches[1]);
    }
}
