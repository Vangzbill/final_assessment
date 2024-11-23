<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkorderNodelink extends Model
{
    protected $table = 'tbl_workorder_nodelink';

    protected $fillable = [
        'id',
        'workorder_id',
        'kontrak_nodelink_id',
        'created_date',
    ];

    public $timestamps = false;
}
