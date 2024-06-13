<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [

        'name',
        'category_id',
        'weight',
        'price',
        'photo_id',
        'photo_path',
        'is_active',
        'live_synced',
        'sku'

    ];


    public  function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function flavours()
    {
        return $this->belongsToMany('App\Flavour');

    }
    public function materials()
    {
        return $this->belongsToMany('App\Material');

    }
    public function photo()
    {
        return $this->belongsTo('App\Photo');

    }

    public function orders()
    {
        return $this->belongsToMany('App\Order')->withPivot('product_name');
    }


}
