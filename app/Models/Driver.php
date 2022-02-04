<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\UserPivot;

class Driver extends Model
{
    protected $table = 'f_trans_figures';
    protected $primaryKey = 'id_trans_figure';
    protected $fillable = [
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

    public function userPivot() {
        return $this->hasMany(UserPivot::class, 'trans_figure_id');
    }

    public function users()
    {
        // $this->hasMany(UserPivot::class, 'carrier_id')->select('user_id')->first()->user_id
        $userPivot = $this->userPivot()->first();
        return $userPivot->user();
    }

    public function FAddress(){
        return $this->hasOne('App\Models\FAddress', 'trans_figure_id');
    }

    public function sat_FAddress(){
        return $this->hasOne('App\Models\Sat\FiscalAddress', 'id', 'fis_address_id');
    }
}
