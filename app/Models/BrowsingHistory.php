<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrowsingHistory extends Model
{
    protected $primaryKey = 'BrowsingHistoryID';

    protected $fillable = [
        'CustomerID',
        'PetID',
        'viewed_at'
    ];

    public $timestamps = true;
}
