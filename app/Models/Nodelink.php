<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nodelink extends Model
{
    protected $table = 'tbl_nodelink';

    protected $fillable = [
        'id',
        'kontrak_nodelink_id',
        'sid',
        'service_line',
        'created_date',
        'workorder_nodelink_id',
        'status_nodelink',
        'latitude',
        'longitude',
    ];

    public $timestamps = false;
}
