<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LunchKitItem extends Model
{
    protected $table = "lunch_kit_items";

    public $timestamps = false;
    protected $fillable = [
        'lunch_kit_id',
        'item_id',
        'quantity',
        'total_price'


    ];
    public function lunchkit()
    {
        return $this->belongsTo(LunchKit::class, 'lunch_kit_id', 'lunch_kit_id');
    }
}
