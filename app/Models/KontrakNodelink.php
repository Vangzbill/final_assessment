<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakNodelink extends Model
{
    protected $table = 'tbl_kontrak_nodelink';

    protected $fillable = [
        'id',
        'kontrak_id',
        'nodelink_id',
        'nama_perusahaan',
        'latitude',
        'longitude',
        'total_biaya',
        'created_date',
    ];

    public $timestamps = false;
}
