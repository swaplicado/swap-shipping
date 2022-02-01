<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
