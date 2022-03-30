<?php

namespace App\Models\M;

use Jenssegers\Mongodb\Eloquent\Model;

class MRequestLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_request_log';
    protected $primaryKey = '_id';

    protected $fillable = [
        'shipping_folio',
        'request_body',
        'response_code',
        'response_message',
        'document_id',
        'mongo_document_id',
        'dt_request',
    ];
}
