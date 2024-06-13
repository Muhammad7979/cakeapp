<?php

namespace App;

use App\PosSale;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    //
    protected $primaryKey = "order_number";
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';


    protected $fillable = [
        'salesman',
        'customer_name',
        'live_synced',
        'customer_email',
        'customer_address',
        'customer_phone',
        'weight',
        'quantity',
        'total_price',
        'advance_price',
        'payment_type',
        'payment_status',
        'order_type',
        'order_status',
        'delivery_date',
        'delivery_time',
        'remarks',
        'branch_id',
        'branch_code',
        'assigned_to',
        'order_number',
        'user_id',
        'is_active',
        'priority',
        'photo_id',
        'photo_path',
        'server_sync',
        'created_at',
        'updated_at',
        'is_custom',
        'discount',
        'instructions',
        'final_image',
        'pending_amount',
        'pending_amount_paid_date',
        'pending_amount_paid_time',
        'payment_status',
        'pending_amount_paid_branch',
        'delivery_sms',
        'delivery_sms_response',
        'order_date',
        'with_positems_price',
        'is_cake',
    ];



    public function products()
    {
        return $this->belongsToMany('App\Product', 'order_product', 'order_number', 'product_sku', "")->withTimestamps();
    }
    public function flavours()
    {
        return $this->belongsToMany('App\Flavour', 'flavour_order', 'order_number', 'flavour_sku');
    }
    public function materials()
    {
        return $this->belongsToMany('App\Material', 'material_order', 'order_number', 'material_sku');
    }
    public function orderStatus()
    {
        return $this->belongsTo('App\OrderStatus', 'order_status');

    }
    public function orderType()
    {

        return $this->belongsTo('App\OrderType', 'order_type');
    }
    public function paymentType()
    {
        return $this->belongsTo('App\PaymentType', 'payment_type');

    }
    public function photo()
    {
        return $this->belongsTo('App\Photo');

    }

    public function branch()
    {
        return $this->belongsTo('App\Branch');
    }
    public function sale()
    {
        return $this->hasOne(PosSale::class, 'order_id', 'id');
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class, 'order_number', 'order_number');
    }

}
