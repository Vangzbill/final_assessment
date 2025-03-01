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
                ->whereNotNull('nama_node')
                ->where('payment_status', 2)
                ->whereHas('order_status_history', function ($query) {
                    $query->where('status_id', 7);
                })
                ->select('nama_node')->get();

            return $this->generateResponse('success', 'Data berhasil diambil', $nodelink);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function all(Request $request) {
        try {
            if (!$token = JWTAuth::getToken()) {
                return $this->generateResponse('error', 'Token not provided', null, 401);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $status = $request->filled('status') ? $request->status : null; // Jika tidak ada, biarkan NULL

            $nodelinkQuery = Order::where('customer_id', $user->id)
                ->whereNotNull('nama_node')
                ->where('payment_status', 2)
                ->whereHas('order_status_history', function ($query) {
                    $query->where('status_id', 7);
                })
                ->with(['kontrak', 'kontrak.kontrak_layanan', 'kontrak.kontrak_layanan.kontrak_nodelink', 'kontrak.kontrak_layanan.kontrak_nodelink.nodelink'])
                ->with(['kontrak.kontrak_layanan.kontrak_nodelink.nodelink' => function ($query) use ($status) {
                    $query->whereNotNull('nama_node');
                    if (!is_null($status)) {
                        $query->where('status_nodelink', $status);
                    }
                }])
                ->get()
                ->map(function ($order) {
                    return $order->kontrak->map(function ($kontrak) {
                        return $kontrak->kontrak_layanan->map(function ($layanan) {
                            return $layanan->kontrak_nodelink->map(function ($nodelink) {
                                return $nodelink->nodelink;
                            });
                        });
                    });
                })->flatten(4)->filter();

            return $this->generateResponse('success', 'Data berhasil diambil', $nodelinkQuery);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

}
