<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqProduct extends Model
{
    protected $table = 'tbl_faq_kategori_produk';

    public function kategori_produk()
    {
        return $this->belongsTo(ProductCategory::class, 'kategori_produk_id');
    }
}
