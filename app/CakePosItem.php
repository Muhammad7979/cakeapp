<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakePosItem extends Model
{
    //
    protected $table = "cake_pos_item";
    public $timestamps = false;
    protected $fillable = [

        'order_number',
        'sale_id',
        'id',
        'fbr_invoice_number',
        'branch_code',
        'order_id'

    ];

}