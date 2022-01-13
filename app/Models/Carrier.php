<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    
    protected $table = 'f_carriers';
    protected $primaryKey = 'id_carrier';
    protected $fillable = [
        'fullname',
        'fiscal_id',
        'is_deleted',
        'usr_new_id',
        'usr_upd_id'
    ];
}
