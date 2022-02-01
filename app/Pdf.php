<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'pdfs';

    protected $fillable = [
        'name',
        'pdf'
    ];
}
