<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workorder extends Model
{
    protected $table = 'tbl_workorder';

    protected $fillable = [
        'id',
        'kontrak_id',
        'nomor',
        'created_date',
    ];

    public $timestamps = false;
}
