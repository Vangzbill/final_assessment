<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'tbl_riwayat_status_order';

    protected $fillable = [
        'id',
        'order_id',
        'status_id',
        'keterangan',
        'tanggal',
    ];

    public $timestamps = false;
}
