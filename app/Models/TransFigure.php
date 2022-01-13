<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransFigure extends Model
{
    protected $table = 'f_trans_figures';
    protected $primaryKey = 'id_trans_figure';
    protected $fillable = [
        'fullname', 
        'fiscal_id', 
        'fiscal_fgr_id', 
        'driver_lic', 
        'is_deleted', 
        'tp_figure_id', 
        'fis_address_id', 
        'carrier_id',
        'usr_new_id',
        'usr_upd_id'
    ];

    public function FAddress(){
        return $this->hasOne('App\Models\FAddress', 'trans_figure_id');
    }

    public function sat_FAddress(){
        return $this->hasOne('App\Models\Sat\FiscalAddress', 'id', 'fis_address_id');
    }
}
