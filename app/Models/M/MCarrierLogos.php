<?php

namespace App\Models\M;

use Jenssegers\Mongodb\Eloquent\Model;

class MCarrierLogos extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'carriers_logos';
    protected $primaryKey = '_id';

    protected $fillable = [
        'carrier_id',
        'image_64',
        'extension'
    ];
}
