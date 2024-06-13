<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flavour extends Model
{
    //


    protected $fillable =
        [   'id',
            'name',
            'sku',
            'price',
            'flavourCategory_id',
            'is_active'

        ];


    public  function flavourCategory()
    {

        return $this->belongsTo('App\FlavourCategory','flavourCategory_id');

    }

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }
    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }
}
