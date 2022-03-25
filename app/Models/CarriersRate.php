<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarriersRate extends Model
{
    protected $table = 'f_carriers_rates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'rate',
        'carrier_id',
        'veh_type_id',
        'mun_id',
        'state_id',
        'is_official',
        'is_reparto'
    ];
}
