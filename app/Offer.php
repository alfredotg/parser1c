<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'city_id',
        'quantity',
        'price',
    ];
}
