<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = "items";

    public $timestamps = false; 
    protected $fillable = [
        'item_id',
        'name',
        'category',
        'supplier_id',
        'item_number',
        'description',
        'cost_price',
        'unit_price',
        'reorder_level',
        'receiving_quantity',
        'pic_id',
        'allow_alt_description',
        'is_serialized',
        'deleted',
        'custom1',
        'custom2',
        'custom3',
        'custom4',
        'custom5',
        'custom6',
        'custom7',
        'custom8',
        'custom9',
        'custom10'
    ];
}
