<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontrak extends Model
{
    protected $table = 'tbl_kontrak';

    protected $fillable = [
        'id',
        'order_id',
        'customer_id',
        'cp_customer_id',
        'nomor_kontrak',
        'project_name',
        'start_kontrak',
        'end_kontrak',
    ];

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
