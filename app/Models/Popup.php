<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    protected $table = 'tbl_popup';

    protected $fillable = [
        'customer_id',
        'created_at'
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
