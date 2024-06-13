<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable =
        [   'id',
            'name',
            'parent_id',
            'description',
            'photo_id',
            'photo_path',
            'is_active'

        ];
    public function photo()
    {
        return $this->belongsTo('App\Photo');

    }
    public function parent()
    {
        return $this->belongsTo('App\Category', 'parent_id');
    }
    public function children()
    {
        return $this->hasMany('App\Category', 'parent_id');
    }


}
