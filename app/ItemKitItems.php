<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemKitItems extends Model
{
    //
    protected $table = "ospos_item_kit_items";

    public $timestamps = false; 
    protected $fillable = [
     

        'item_kit_id',
        'item_id',
        'quantity',

    ];
}
