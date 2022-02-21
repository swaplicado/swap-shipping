<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f_certificates';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_certificate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_valid_from',
        'dt_valid_to',
        'cert_number',
        'carrier_id',
        'usr_new_id',
        'usr_upd_id',
    ];
}
