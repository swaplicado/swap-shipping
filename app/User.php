<?php

namespace App;

use App\Role;
use App\RolePermission;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = "users";
    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'full_name', 'user_type_id', 'is_deleted'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',  'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function getRol() {
        return $this->roles()->get();
    }

    public function authorizeRoles($roles) {
        abort_unless($this->hasAnyRole($roles), 401);
        return true;
    }

    public function hasAnyRole($roles) {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true; 
            }   
        }
        return false;
    }

    public function hasRole($role) {
        if ($this->roles()->where('name', $role)->first()) {
            return true;
        }
        return false;
    }

    public function authorizePermission($permissions) {
        abort_unless($this->hasAnyPermission($permissions), 401);
        return true;
    }    

    public function hasAnyPermission($permissions) {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->hasPermission($permission)) {
                    return true;
                }
            }
        } else {
            if ($this->hasPermission($permissions)) {
                return true; 
            }   
        }
        return false;
    }

    public function hasPermission($permission) {
        $roles = $this->getRol();
        foreach($roles as $role){
            $rolePermissions = $role->RolePermissions()->get();
            foreach($rolePermissions as $rolePermission){
                if($rolePermission->Permission()->where('key_code', $permission)->first()){
                    return true;
                    break;
                }
            }
        }
        return false;
    }
}
