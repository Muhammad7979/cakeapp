<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakeSuspendedItemsTaxes extends Model
{
    //
    protected $table = "ospos_cake_suspended_items_taxes";
    public $timestamps = false; 
    protected $fillable = [
     
        'sale_id',
        'item_id',
        'line',
        'name',
        'percent'

    ];
}
