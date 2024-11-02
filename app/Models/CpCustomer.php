<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpCustomer extends Model
{
    use HasFactory;

    protected $table = 'tbl_cp_customer';

    protected $fillable = [
        'id',
        'customer_id',
        'nama',
        'email',
        'no_telp',
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'cp_customer_id', 'id');
    }
}
