<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakePosItemTemp extends Model
{
    //
    protected $table = "cake_positem_temp";
    public $timestamps = false; 
    protected $fillable = [
     
        'order_number',
        'sale_id',
        'id',
        'fbr_invoice_number',
        'branch_code'

    ];

}
