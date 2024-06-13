<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'is_active'
    ];
    public function user()
    {

        return $this->belongsTo(User::class,'branch_users');

    }
}
