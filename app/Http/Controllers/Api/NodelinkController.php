<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NodelinkController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function index()
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return $this->generateResponse('error', 'Token not provided', null, 401);
            }

            $user = JWTAuth::parseToken()->authenticate();

            $nodelink = Order::where('customer_id', $user->id)
                ->whereNotNull('sid')
                ->where('payment_status', 2)
                ->whereHas('order_status_history', function ($query) {
                    $query->where('status_id', 7);
                })
                ->select('sid')->get();

            return $this->generateResponse('success', 'Data berhasil diambil', $nodelink);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

}
