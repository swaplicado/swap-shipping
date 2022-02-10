<?php

namespace App\Models\M;

use Jenssegers\Mongodb\Eloquent\Model;

class MSignLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sign_cfdi_log';
    protected $primaryKey = '_id';

    protected $fillable = [
        'message',
        'idError',
        'mongoDocumentId',
        'idDocument',
        'idUser',
    ];
}
