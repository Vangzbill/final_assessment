<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceItem extends Model
{
    protected $table = 'tbl_proforma_invoice_item';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'keterangan',
        'tanggal',
        'layanan_id',
        'proforma_invoice_id',
        'nilai_pokok',
        'nilai_ppn',
        'total_bayar',
        'quantity',
        'produk_id',
    ];
}
