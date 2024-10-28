<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Product list',
            'data' => [
                [
                    'id' => 1,
                    'name' => 'Product 1',
                    'price' => 10000,
                ],
                [
                    'id' => 2,
                    'name' => 'Product 2',
                    'price' => 20000,
                ],
                [
                    'id' => 3,
                    'name' => 'Product 3',
                    'price' => 30000,
                ],
            ],
        ]);
    }
}
