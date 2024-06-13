<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakeSuspendedPayments extends Model
{
    //
    protected $table = "ospos_cake_suspended_payments";
    public $timestamps = false; 
    protected $fillable = [
     
        'sale_id',
        'payment_type',
        'cake_total',
        'cake_advance',
        'cake_balance',
        'pos_items_total',
        'pos_items_advance',
        'pos_items_balance',
        'payment_amount',

    ];
}
