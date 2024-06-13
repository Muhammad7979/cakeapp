<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSaleItemsOnline extends Model
{
    //
    protected $connection = 'online';
    protected $table = "sales_items";

    public $timestamps = false; 
    protected $fillable = [
     

        'sale_id',
        'item_id',
        'description',
        'serialnumber',
        'line',
        'quantity_purchased',
        'item_cost_price',
        'item_unit_price',
        'discount_percent',
        'item_location',
        
    ];
}
