<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f_documents';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_document';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_request',
        'dt_generated',
        'comp_version',
        'xml_version',
        'is_processed',
        'is_deleted',
        'mongo_document_id',
        'carrier_id',
        'usr_gen_id',
        'usr_sign_id',
        'usr_can_id',
        'usr_new_id',
        'usr_upd_id'
    ];
}
