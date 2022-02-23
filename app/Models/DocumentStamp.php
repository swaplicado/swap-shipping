<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStamp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'f_documents_stamps';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_doc_stamp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dt_stamp',
        'stamp_type',
        'document_id',
        'usr_new_id',
    ];
}
