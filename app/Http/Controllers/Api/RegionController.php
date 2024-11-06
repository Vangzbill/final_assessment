<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RegionController extends Controller
{
    private function generateResponse($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function provinsi(){
        $response = Http::withOptions(['verify' => false])->get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')->json();

        return $this->generateResponse('success', 'Data fetched successfully', $response);
    }

    public function kabupaten($id){
        $response = Http::withOptions(['verify' => false])->get('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/'. $id .'.json')->json();

        return $this->generateResponse('success', 'Data fetched successfully', $response);
    }

    public function kecamatan($id){
        $response = Http::withOptions(['verify' => false])->get('https://www.emsifa.com/api-wilayah-indonesia/api/districts/'. $id .'.json')->json();

        return $this->generateResponse('success', 'Data fetched successfully', $response);
    }

    public function kelurahan($id){
        $response = Http::withOptions(['verify' => false])->get('https://www.emsifa.com/api-wilayah-indonesia/api/villages/'. $id .'.json')->json();

        return $this->generateResponse('success', 'Data fetched successfully', $response);
    }
}
