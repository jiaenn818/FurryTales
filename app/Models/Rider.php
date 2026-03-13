<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $table = 'rider';

    protected $primaryKey = 'riderID';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'riderID',
        'userID',
        'postCode',
    ];

    public $timestamps = true; 

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ───────── RELATIONSHIPS ───────── */

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'riderID', 'riderID');
    }

}
