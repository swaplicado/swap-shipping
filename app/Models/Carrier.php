<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\UserVsTypes;

class Carrier extends Model
{
    
    protected $table = 'f_carriers';
    protected $primaryKey = 'id_carrier';
    protected $fillable = [
        'fullname',
        'fiscal_id',
        'telephone1',
        'contact1',
        'telephone2',
        'contact2',
        'is_deleted',
        'tax_regimes_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function UserVsTypes() {
        return $this->hasMany(UserVsTypes::class, 'carrier_id');
    }

    public function users()
    {
        // $this->hasMany(UserVsTypes::class, 'carrier_id')->select('user_id')->first()->user_id
        $UserVsTypes = $this->UserVsTypes()->first();
        return $UserVsTypes->user();
    }
    
    public function tax_regime(){
        return $this->hasOne('App\Models\Sat\tax_regimes', 'id', 'tax_regimes_id');
    }

    public function parners(){
        return $this->hasMany(UserVsTypes::class, 'carrier_id')->where([['is_principal', 0]]);
    }
}
