<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    //
    protected $fillable =
        [
            'id',
            'name',
            'price',
            'sku',
            'is_active'

        ];

    public function orders()
    {
        return $this->belongsToMany('App\Order');
    }
}
