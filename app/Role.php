<?php

namespace App;

use App\User;
use App\RolePermission;
use App\RoleUser;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function RolePermissions(){
        return $this->hasMany('App\RolePermission', 'role_id')->where('is_deleted', '!=', 1);
    }

    public function Roles(){
        return $this->hasMany('App\Role', 'id')->where('is_deleted', '!=', 1);
    }
}
