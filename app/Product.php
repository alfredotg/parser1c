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

    public function offers()
    {
        return $this->hasMany('App\Offer');
    }
}
