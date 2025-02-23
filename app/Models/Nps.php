<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nps extends Model
{
    protected $table = 'tbl_nps';

    protected $fillable = [
        'customer_id',
        'nama_customer',
        'rating',
        'feedback',
        'created_date'
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
