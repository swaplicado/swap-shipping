<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurances extends Model
{
    protected $table = 'f_insurances';
    protected $primaryKey = 'id_insurance';
    protected $fillable = [
        'full_name', 
        'is_civ_resp',
        'is_ambiental',
        'is_cargo',
        'is_deleted',
        'carrier_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function Carrier(){
        return $this->hasOne('App\Models\Carrier', 'id_carrier', 'carrier_id');
    }
}
