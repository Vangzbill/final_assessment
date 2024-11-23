<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatDeposit extends Model
{
    protected $table = 'tbl_riwayat_deposit';

    protected $fillable = [
        'id',
        'order_id',
        'tipe',
        'jumlah',
        'tgl_deposit',
    ];

    public $timestamps = false;
}
