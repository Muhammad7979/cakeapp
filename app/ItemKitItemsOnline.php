<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemKitItemsOnline extends Model
{
    //
    protected $connection = 'online';
    protected $table = "item_kit_items";

    public $timestamps = false; 
    protected $fillable = [
     

        'item_kit_id',
        'item_id',
        'quantity',

    ];
}
