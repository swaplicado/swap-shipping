<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportPart extends Model
{
    protected $table = 'sat_transp_parts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'key_code',
        'description'
    ];
}
