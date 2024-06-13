<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //

    protected $fillable = [
        'name', 'slug', 'permissions',

    ];

    protected $casts = [
        'permissions' => 'array',
    ];

//    Relationship for users and roles defined in the following Method

    public function users()
    {
        return $this->belongsTo(User::class);
    }


    // for checking rather the users has the access to perform a certain task...

    public function hasAccess(array $permissions) : bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission))
                return true;
        }
        return false;
    }

    //checks rather the current users has the access to perform a certain task..

    public function hasPermission(string $permission) : bool
    {
        return $this->permissions[$permission] ?? false;
    }





}
