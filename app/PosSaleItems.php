<?php

namespace App;

use App\Item;

use Illuminate\Database\Eloquent\Model;

class PosSaleItems extends Model
{
    //
    protected $table = "pos_sales_items";

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

    public function items()
    {
        return $this->hasMany(Item::class, 'sale_id', 'sale_id');
    }
}
