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

    public function status()
    {
        return $this->belongsTo(StatusOrder::class, 'status_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
