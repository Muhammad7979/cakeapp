<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSaleItemKitItems extends Model
{
    //
    protected $table = "pos_sales_item_kit_items";

    public $timestamps = false; 
    protected $fillable = [
     

        'item_kit_id',
        'item_id',
        'quantity',
        'kit_quantity',
        'cake_invoice',


    ];

    public function kit(){

        return $this->belongsTo(ItemKits::class,'item_kit_id','item_kit_id');

    }

    public function items(){
        return $this->belongsTo(Item::class,'item_id','item_id');
    }
}
