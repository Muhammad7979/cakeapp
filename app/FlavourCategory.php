<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlavourCategory extends Model
{
    //
    protected $fillable =
        [
        'id',
            'name',
            'description',
            'is_active'

        ];


    public function flavours()
    {
        return $this->belongsToMany(Flavour::class);
    }
}
