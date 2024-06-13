<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakeSuspendedItemsTaxesOnline extends Model
{
    //
    protected $connection = 'online';
    protected $table = "cake_suspended_items_taxes";
    public $timestamps = false; 
    protected $fillable = [
     
        'sale_id',
        'item_id',
        'line',
        'name',
        'percent'

    ];
}
