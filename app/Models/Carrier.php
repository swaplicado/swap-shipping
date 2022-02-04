<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\UserPivot;

class Carrier extends Model
{
    
    protected $table = 'f_carriers';
    protected $primaryKey = 'id_carrier';
    protected $fillable = [
        'fullname',
        'fiscal_id',
        'is_deleted',
        'tax_regimes_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function userPivot() {
        return $this->hasMany(UserPivot::class, 'carrier_id');
    }

    public function users()
    {
        // $this->hasMany(UserPivot::class, 'carrier_id')->select('user_id')->first()->user_id
        $userPivot = $this->userPivot()->first();
        return $userPivot->user();
    }
    
    public function tax_regime(){
        return $this->hasOne('App\Models\Sat\tax_regimes', 'id', 'tax_regimes_id');
    }
}
