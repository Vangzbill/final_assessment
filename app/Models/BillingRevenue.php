<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingRevenue extends Model
{
    protected $table = 'tbl_billing_revenue';

    protected $fillable = [
        'id',
        'kontrak_nodelink_id',
        'order_id',
        'tanggal_tagih',
        'total_ppn',
        'total_tagihan',
        'total_akhir',
        'jatuh_tempo',
        'status',
    ];

    public $timestamps = false;

    public function kontrak_nodelink()
    {
        return $this->belongsTo(KontrakNodelink::class, 'kontrak_nodelink_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
