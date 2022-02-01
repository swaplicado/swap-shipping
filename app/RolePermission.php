<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;
use App\Permission;

class RolePermission extends Model
{
    protected $table = 'role_permissions';
    protected $fillable = [
        'role_id',
        'permission_id',
        'is_deleted'
    ];

    public function Permission(){
        return $this->hasOne('App\Permission', 'id', 'permission_id');
    }
}
