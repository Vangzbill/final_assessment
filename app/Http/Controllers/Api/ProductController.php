<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            $product = ProductCategory::select('id', 'nama_kategori as nama', 'deskripsi')->get();

            $product->map(function ($item) {
                $item->image = '';
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
                $service = Service::select('nama_layanan as nama', 'harga_layanan')
                ->distinct()
                ->get();

                $data = [
                    'id' => $product->id,
                    'spesifikasi' => $product->spesifikasi,
                    'perangkat' => $perangkat,
                    'layanan' => $service
                ];
                return $this->generateResponse('success', 'Data retrieved successfully', $data);
            } else {
                return $this->generateResponse('error', 'Data not found', null, 404);
            }
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }


}
