<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\UserVsTypes;

class Driver extends Model
{
    protected $table = 'f_trans_figures';
    protected $primaryKey = 'id_trans_figure';
    protected $fillable = [
        'alias',
        'fullname', 
        'fiscal_id', 
        'fiscal_fgr_id', 
        'driver_lic', 
        'is_deleted', 
        'tp_figure_id', 
        'fis_address_id', 
        'carrier_id',
        'usr_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function UserVsTypes() {
        return $this->hasMany(UserVsTypes::class, 'trans_figure_id');
    }

    public function users()
    {
        // $this->hasMany(UserVsTypes::class, 'carrier_id')->select('user_id')->first()->user_id
        $UserVsTypes = $this->UserVsTypes()->first();
        return $UserVsTypes->user();
    }

    public function FAddress(){
        return $this->hasOne('App\Models\FAddress', 'trans_figure_id');
    }

    public function sat_FAddress(){
        return $this->hasOne('App\Models\Sat\FiscalAddress', 'id', 'fis_address_id');
    }

    public function Carrier(){
        return $this->hasOne('App\Models\Carrier', 'id_carrier', 'carrier_id');
    }
}
