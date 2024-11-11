<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusOrder extends Model
{
    protected $table = 'tbl_status_order';

    protected $fillable = [
        'id',
        'status',
    ];

    public $timestamps = false;

    public function order_status_history()
    {
        return $this->hasMany(OrderStatusHistory::class, 'status_id', 'id');
    }
}
