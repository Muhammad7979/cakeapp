<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemQuantity extends Model
{
    //
    protected $table = "item_quantities";
    public $timestamps = false;
    protected $fillable = [
        'item_id',
        'location_id',
        'quantity',


    ];
}
