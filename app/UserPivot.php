<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Carrier;
use App\Models\Driver;

class UserPivot extends Model
{
    protected $table = 'user_pivotes';
    protected $primarykey = 'id';

    protected $fillable = [
        'is_deleted',
        'trans_figure_id',
        'carrier_id',
        'user_id'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function carrier(){
        return $this->hasOne(Carrier::class, 'id_carrier', 'carrier_id');
    }
    public function driver(){
        return $this->hasOne(Driver::class, 'id_trans_figure', 'trans_figure_id');
    }
}
