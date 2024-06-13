<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSaleOnline extends Model
{
    //
    protected $connection = 'online';
    protected $table = "sales";
    protected $primaryKey = 'sale_id';
    public $timestamps = false; 
    protected $fillable = [
     
        'exact_time',
        'sale_time',
        'customer_id',
        'employee_id',
        'sale_type',
        'sale_payment',
        'branch_code',
        'comment',
        'fbr_fee',
        'invoice_number',
        'fbr_invoice_number',
        'sale_id'

    ];
}
