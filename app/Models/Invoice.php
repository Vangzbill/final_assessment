<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'tbl_invoice';

    protected $fillable = [
        'id',
        'kontrak_id',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
    ];

    public $timestamps = false;
}
