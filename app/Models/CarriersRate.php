<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarriersRate extends Model
{
    protected $table = 'f_carriers_rates';
    protected $primaryKey = 'id';

    protected $fillable = [
        'carrier_id',
        'origen_id',
        'Local_foreign',
        'veh_type_id',
        'state_id',
        'zone_state_id',
        'mun_id',
        'zone_mun_id',
        'zip_code',
        'id_rate',
        'rate'
    ];
}
