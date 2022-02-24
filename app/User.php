<?php

namespace App;

use App\Role;
use App\RoleUser;
use App\RolePermission;
use App\UserVsTypes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Notifications\VerifyEmail;
use App\Notifications\PasswordReset;

class User extends Authenticatable implements MustVerifyEmail
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
        return $this->belongsToMany(Role::class);
    }

    public function getRol() {
        return $this->roles()->get();
    }

    public function getRoles() {
        return $this->belongsToMany(Role::class)->wherePivot('is_deleted', 0);
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
        if ($this->getRoles()->where('name', $role)->first()) {
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
        $roles = $this->getRoles()->get();
        foreach($roles as $role){
            $rolePermissions = $role->RolePermissions()->get();
            foreach($rolePermissions as $rolePermission){
                if($rolePermission->Permission()->where('key_code', $permission)->first()){
                    return true;
                    break;
                }
            }
        }
    }

    public function UserVsTypes() {
        return $this->hasMany(UserVsTypes::class, 'user_id');
    }

    public function carrier() {
        $UserVsTypes = $this->UserVsTypes()->first();
        return $UserVsTypes->carrier();
    }

    public function driver() {
        $UserVsTypes = $this->UserVsTypes()->first();
        return $UserVsTypes->driver();
    }

    public function isCarrier(){
        return $this->user_type_id == 3;
    }
    
    public function isDriver(){
        return $this->user_type_id == 4;
    }
    
    public function isAdmin(){
        return $this->user_type_id == 1;
    }

    public function isClient(){
        return $this->user_type_id == 2;
    }

    public function carrierAutorization($carriers){
        if(!($this->isAdmin() || $this->isClient())){
            abort_unless($this->hasAnyCarrier($carriers), 401);
        }
        return true;
    }

    public function hasAnyCarrier($carriers) {
        if (is_array($carriers)) {
            foreach ($carriers as $carrier) {
                if ($this->hasCarrier($carrier)) {
                    return true;
                }
            }
        } else {
            if ($this->hasCarrier($carriers)) {
                return true; 
            }   
        }
        return false;
    }
    
    public function hasCarrier($carrier) {
        return $this->carrier()->first()->id_carrier == $carrier;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail($this->tempPass)); // my notification
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }
}
