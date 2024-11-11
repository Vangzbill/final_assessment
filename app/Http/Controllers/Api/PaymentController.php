<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function midtransCallback(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            $orderId = $request->order_id;
            $paymentMidtrans = Payment::paymentGatewayMidtrans($orderId, $user);
            if ($paymentMidtrans) {
                return $this->generateResponse('success', 'Payment success', $paymentMidtrans, 200);
            } else {
                return $this->generateResponse('error', 'Payment failed', null, 500);
            }
        } else {
            return $this->generateResponse('error', 'Unauthorized', null, 401);
        }
    }
}
