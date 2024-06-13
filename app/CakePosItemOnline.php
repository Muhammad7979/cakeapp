<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakePosItemOnline extends Model
{
    //
    protected $connection = 'online';
    protected $table = "cake_pos_item";
    public $timestamps = false; 
    protected $fillable = [
     
        'order_number',
        'sale_id',
        'id',
        'fbr_invoice_number',
        'branch_code',

    ];
}
