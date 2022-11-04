<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trailer extends Model
{
    protected $table = 'f_trailers';
    protected $primaryKey = 'id_trailer';
    protected $fillable = [
        'plates',
        'is_own',
        'is_deleted',
        'trailer_subtype_id',
        'trans_part_n_id',
        'carrier_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function TrailerSubtype()
    {
        return $this->hasOne('App\Models\Sat\TrailerSubtype', 'id', 'trailer_subtype_id');
    }

    public function Carrier()
    {
        return $this->hasOne('App\Models\Carrier', 'id_carrier', 'carrier_id');
    }

}
