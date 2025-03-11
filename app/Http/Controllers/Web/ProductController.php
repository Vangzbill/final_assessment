<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = ProductCategory::select('id', 'nama_kategori as nama', 'deskripsi', 'image')->get()->values();
        return view('pages.feature.product', compact('product'));
    }

}
