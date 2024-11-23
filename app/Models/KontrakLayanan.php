<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontrakLayanan extends Model
{
    protected $table = 'tbl_kontrak_layanan';

    protected $fillable = [
        'id',
        'kontrak_id',
        'layanan_id',
        'produk_id',
        'jumlah_node'
    ];

    public $timestamps = false;
}
