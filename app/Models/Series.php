<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $table = 'f_document_series';
    protected $primaryKey = 'id_serie';
    protected $fillable = [
        'serie_name',
        'prefix',
        'initial_number',
        'description',
        'is_deleted',
        'carrier_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function Carrier(){
        return $this->hasOne('App\Models\Carrier', 'id_carrier', 'carrier_id');
    }
}
