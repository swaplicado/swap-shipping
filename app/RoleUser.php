<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Role;

class RoleUser extends Model
{
    protected $table = 'role_user';
    protected $primarykey = 'id';

    protected $fillable = ['role_id', 'user_id', 'is_deleted'];

    public function RoleUser(){
        dd($this->hasMany(Role::Class)->get());
    }

    public function roles(){
        return $this->hasMany('App\Role', 'id')->where('is_deleted', '!=', 1)->get();
    }
}
