<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemTax extends Model
{
    //
    protected $table = "items_taxes";
    public $timestamps = false;
    protected $fillable = [
        'item_id',
        'name',
        'percent',


    ];
}
