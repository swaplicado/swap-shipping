<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'f_vehicles';
    protected $primaryKey = 'id_vehicle';
    protected $fillable = [
        'alias',
        'plates',
        'year_model',
        'license_sct_num',
        'policy',
        'license_sct_id',
        'veh_cfg_id',
        'veh_key_id',
        'carrier_id',
        'insurance_id',
        'is_deleted',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function LicenceSct(){
        return $this->hasOne('App\Models\Sat\LicenceSct', 'id', 'license_sct_id');
    }

    public function VehicleConfig(){
        return $this->hasOne('App\Models\Sat\VehicleConfig', 'id', 'veh_cfg_id');
    }

    public function VehicleKey(){
        return $this->hasOne('App\Models\VehicleKey', 'id_key', 'veh_key_id');
    }

    public function Carrier(){
        return $this->hasOne('App\Models\Carrier', 'id_carrier', 'carrier_id');
    }

    public function Insurance(){
        return $this->hasOne('App\Models\Insurances', 'id_insurance', 'insurance_id');
    }
}
