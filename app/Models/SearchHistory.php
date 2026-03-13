<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    protected $table = 'search_histories';

    protected $primaryKey = 'SearchHistoryID';

    protected $fillable = [
        'CustomerID',
        'keyword',
        'searched_at'
    ];

    public $timestamps = true;
}
