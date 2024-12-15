<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FaqProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function generateResponse($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function index()
    {
        try {
            $product = ProductCategory::select('id', 'nama_kategori as nama', 'deskripsi', 'image')->get();

            $product->map(function ($item) {
                $imagePath = public_path('assets/images/' . $item->image);

                if (file_exists($imagePath) && $item->image) {
                    $item->image = url('assets/images/' . $item->image);
                } else {
                    $item->image = url('assets/images/default.png');
                }
            });

            return $this->generateResponse('success', 'Data retrieved successfully', $product);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function show($id)
    {
        try {
            $product = ProductCategory::find($id);
            if ($product) {
                $perangkat = Product::where('kategori_produk_id', $id)->get();

                $perangkat->map(function ($item) {
                    $imagePath = public_path('assets/images/' . $item->gambar_produk);
                    $item->gambar_produk = file_exists($imagePath) && $item->gambar_produk
                        ? url('assets/images/' . $item->gambar_produk)
                        : url('assets/images/default.png');
                    return $item;
                });

                $service = Service::select('nama_layanan as nama', 'harga_layanan')
                    ->distinct()
                    ->get();

                $imagePath = public_path('assets/images/' . $product->image);
                $imageUrl = file_exists($imagePath) && $product->image
                    ? url('assets/images/' . $product->image)
                    : url('assets/images/default.png');

                $data = [
                    'id' => $product->id,
                    'nama' => $product->nama_kategori,
                    'spesifikasi' => $product->spesifikasi,
                    'image' => $imageUrl,
                    'perangkat' => $perangkat,
                    'layanan' => $service,
                ];

                return $this->generateResponse('success', 'Data retrieved successfully', $data);
            } else {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }


    public function faqProduct($id){
        try {
            $data_faq = FaqProduct::where('kategori_produk_id', $id)->get();

            return $this->generateResponse('success', 'Data retrieved successfully', $data_faq, 200);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
