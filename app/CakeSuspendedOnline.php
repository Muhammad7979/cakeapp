<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CakeSuspendedOnline extends Model
{
    //
    protected $connection = 'online';
    protected $table = "cake_suspended";
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

    ];
    public function suspended_items(){
        return $this->hasMany(CakeSuspendedItemsOnline::class, 'sale_id', 'sale_id');
    }
    public function suspended_items_taxes(){
        return $this->hasMany(CakeSuspendedItemsTaxesonline::class, 'sale_id', 'sale_id');
    }
    public function suspended_payments(){
        return $this->hasMany(CakeSuspendedPaymentsOnline::class, 'sale_id', 'sale_id');
    }
}
