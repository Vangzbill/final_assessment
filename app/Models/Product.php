<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'tbl_produk';

    public function order()
    {
        return $this->hasMany(Order::class, 'produk_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'kategori_produk_id', 'id');
    }
}
