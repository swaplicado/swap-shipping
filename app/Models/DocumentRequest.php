<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f_document_requests';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_request';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_request',
        'comp_version',
        'xml_version',
        'body_request_id',
        'is_processed',
        'is_deleted',
        'carrier_id',
        'usr_new_id',
        'usr_upd_id'
    ];
}
