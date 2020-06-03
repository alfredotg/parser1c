<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'id',
        'name',
        'weight',
        'usage',
    ];

    //function folder()
    //{
    //    return $this->belongsTo('App\Folder');
    //}    
}
