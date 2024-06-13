<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PosSaleTemp extends Model
{
    //
    protected $table = "pos_sales_temp";
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
        'sale_id',
        'order_id',
        'cake_invoice',
        'status',
        'second_payment'

    ];

    public function items()
    {
        // Assuming there is a foreign key 'item_kit_id' in ItemKitItemsOnline
        return $this->hasMany(PosSaleItemsTemp::class, 'sale_id','sale_id');
    }
   
}
