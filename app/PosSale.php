<?php

namespace App;

use App\Order;
use Illuminate\Database\Eloquent\Model;

class PosSale extends Model
{
    //

    protected $table = "pos_sales";
    protected $primaryKey = 'sale_id';
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = [
        'order_id',
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
        'second_payment'



    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function items()
    {
        // Assuming there is a foreign key 'item_kit_id' in ItemKitItemsOnline
        return $this->hasMany(PosSaleItems::class, 'sale_id','sale_id');
    }
}
