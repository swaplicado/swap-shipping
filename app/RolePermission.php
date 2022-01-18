<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use App\Permission;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    public function Permission(){
        return $this->hasOne('App\Permission', 'id');
    }
}
