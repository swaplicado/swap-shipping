<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Pdf extends Eloquent
{
    protected $connection = "mongodb";
    protected $collection = 'pdfs';

    protected $fillable = [
        'name',
        'pdf'
    ];
}
