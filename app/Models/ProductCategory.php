<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $table = 'tbl_kategori_produk';

    public function faq_produk()
    {
        return $this->hasMany(FaqProduct::class, 'kategori_produk_id');
    }

    public function produk()
    {
        return $this->hasMany(Product::class, 'kategori_produk_id', 'id');
    }
}
