<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAddress extends Model
{
    protected $table = 'f_addresses';
    protected $primaryKey = 'id_address';
    protected $fillable = [
        'telephone', 
        'street', 
        'street_num_ext', 
        'street_num_int', 
        'neighborhood', 
        'reference', 
        'locality', 
        'state', 
        'zip_code', 
        'is_deleted',
        'trans_figure_id',
        'country_id',
        'state_id',
        'usr_new_id',
        'usr_upd_id'
    ];
}
