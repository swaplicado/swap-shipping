<?php

namespace App;

use App\User;
use App\RolePermission;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function RolePermissions(){
        return $this->hasMany('App\RolePermission', 'role_id');
    }
}
