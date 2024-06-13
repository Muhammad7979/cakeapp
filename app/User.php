<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'role_id',
        'is_admin',
        'is_active',
        'branch_id',
        'photo_id'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    //Relationship between users and roles

    public function role()
    {

        return $this->belongsTo('App\Role');
    }


    /*
     * Checks if users has access to a permission specified
     *
     */


    public function hasAccess(array $permissions): bool
    {
        // check if the permission is available in any role

            if ($this->role->hasAccess($permissions)) {
                return true;
            }

        return false;
    }

    /**
     * Checks if the users belongs to role.
     */
    public function inRole(string $roleSlug)
    {
        return $this->role()->where('slug', $roleSlug)->count() == 1;
    }

    public function isAdmin()
    {

        if ($this->is_admin==1 && $this->is_active ==1){
            return true;
        }
        else
        {
            return false;
        }

    }

    public function photo()
    {
        return $this->belongsTo('App\Photo');

    }

//    public function getIsAdminAttribute()
//    {
//
//        if()
//
//    }
    public function groups()
    {
        return $this->belongsToMany(Group::class,'group_users');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

}
