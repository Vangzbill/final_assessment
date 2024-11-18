<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function createPayment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            $orderId = $request->order_id;
            $paymentMidtrans = Payment::paymentGatewayMidtrans($orderId, $user);
            if ($paymentMidtrans) {
                return $this->generateResponse('success', 'Payment URL generated', $paymentMidtrans, 200);
            } else {
                return $this->generateResponse('error', 'Failed to generate payment', null, 500);
            }
        }
        return $this->generateResponse('error', 'Unauthorized', null, 401);
    }

    public function handleNotification(Request $request)
    {
        try {
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            if ($hashed == $request->signature_key) {
                $order = Order::find($request->order_id);
                if (!$order) {
                    return $this->generateResponse('error', 'Order not found', null, 404);
                }

                switch ($request->transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $order->payment_status = 2;
                        break;
                    case 'pending':
                        $order->payment_status = 1;
                        break;
                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $order->payment_status = 3;
                        break;
                }

                $order->save();
                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }

    public function finishPayment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return $this->generateResponse('error', 'Unauthorized', null, 401);
        }

        $order = Order::where('id', $request->order_id)
                     ->where('user_id', $user->id)
                     ->first();

        $order_history = new OrderStatusHistory();
        $order_history->order_id = $order->id;
        $order_history->status_id = 2;
        $order_history->keterangan = 'Pembayaran berhasil';
        $order_history->tanggal = now();
        $order_history->save();

        if (!$order) {
            return $this->generateResponse('error', 'Order not found', null, 404);
        }

        return $this->generateResponse('success', 'Payment completed', [
            'order_id' => $order->id,
            'status' => $order->payment_status,
            'payment_url' => $order->payment_url
        ], 200);
    }
}
