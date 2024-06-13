<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    //
    protected $table = "order_product";

    protected $fillable = [
     
        'id',
        'order_number',
        'product_sku',
        'product_name',
        'product_price',
        'created_at',
        'updated_at',

    ];

}
