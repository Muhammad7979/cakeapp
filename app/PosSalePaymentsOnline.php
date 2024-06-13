<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSalePaymentsOnline extends Model
{
    //
    protected $connection = 'online';

    protected $table = "sales_payments";

    public $timestamps = false; 
    protected $fillable = [
     

        'sale_id',
        'payment_type',
        'payment_amount',
        


    ];
}
