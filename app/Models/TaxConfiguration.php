<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxConfiguration extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f_tax_configurations';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'config_type',
        'date_from',
        'date_to',
        'person_type_emisor',
        'person_type_receptor',
        'is_neg_fiscal_reg',
        'fiscal_regime_id',
        'is_neg_concept',
        'concept_id',
        'is_neg_group',
        'group_id',
        'tax_id',
        'rate',
        'amount',
        'is_deleted',
        'carrier_id',
        'usr_new_id',
        'usr_upd_id'
    ];
}
