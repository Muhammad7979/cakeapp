<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakeSuspended extends Model
{
    //
    protected $table = "ospos_cake_suspended";
    public $timestamps = false; 
    protected $fillable = [
     
        // 'sale_item',
        'customer_id',
        'employee_id',
        'comment',
        'invoice_number',
        'sale_id',
        'branch_code',
        'cake_invoice',
        'order_id',
        'second_payment'

    ];

    public function suspended_items(){
        return $this->hasMany(CakeSuspendedItems::class, 'sale_id', 'sale_id');
    }
    public function suspended_items_taxes(){
        return $this->hasMany(CakeSuspendedItemsTaxes::class, 'sale_id', 'sale_id');
    }
    public function suspended_payments(){
        return $this->hasMany(CakeSuspendedPayments::class, 'sale_id', 'sale_id');
    }
}
