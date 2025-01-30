<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $table = 'tbl_proforma_invoice';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'order_id',
        'no_proforma_invoice',
        'tanggal_proforma',
        'tanggal_jatuh_tempo',
        'biaya_perangkat',
        'deposit_layanan',
        'biaya_pengiriman',
        'ppn',
        'total_keseluruhan',
        'url_proforma',
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
