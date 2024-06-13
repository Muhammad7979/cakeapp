<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSalePayments extends Model
{
    //
    protected $table = "pos_sales_payments";

    public $timestamps = false; 
    protected $fillable = [
     

        'sale_id',
        'payment_type',
        'payment_amount',
        


    ];
}
