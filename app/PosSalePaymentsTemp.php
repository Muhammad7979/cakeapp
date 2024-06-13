<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSalePaymentsTemp extends Model
{
    //
    protected $table = "pos_sales_payments_temp";

    public $timestamps = false; 
    protected $fillable = [
     

        'sale_id',
        'payment_type',
        'payment_amount',
        
    ];
}
