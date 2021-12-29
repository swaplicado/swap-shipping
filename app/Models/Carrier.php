<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f_carriers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_carrier';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'fiscal_id',
        'is_deleted'
    ];
}
