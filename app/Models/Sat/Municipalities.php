<?php

namespace App\Models\Sat;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sat\States;

class Municipalities extends Model
{
    protected $table = 'sat_municipalities';
    protected $primaryKey = 'id';
    
    public function state(){
        return $this->hasOne('App\Models\Sat\States', 'id', 'state_id');
    }
}
