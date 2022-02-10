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
        'serie',
        'folio',
        'requested_at',
        'generated_at',
        'canceled_at',
        'signed_at',
        'comp_version',
        'xml_version',
        'uuid',
        'is_processed',
        'is_signed',
        'is_canceled',
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
