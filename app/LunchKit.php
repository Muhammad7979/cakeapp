<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LunchKit extends Model
{
    protected $table = "lunch_kits";

    protected $primaryKey = 'lunch_kit_id';
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'

    ];

    public function lunchkit_items()
    {
        return $this->hasMany(LunchKitItem::class, 'lunch_kit_id', 'lunch_kit_id');
    }
}
