<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransFigCfg extends Model
{
    protected $table = 'f_trans_figures_cfg';
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'trans_part_id',
        'veh_tra_id',
        'figure_type_id',
        'figure_trans_id'
    ];
}
