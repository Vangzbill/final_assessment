<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    // public function success(Request $request)
    // {
    //     try {
    //         $token = Session::get('jwt_token');

    //         $response = new ApiPaymentController();
    //         $response = $response->finishPayment($token);

    //         return view('payment.payment-success', ['token' => $token, 'response' => $response]);
    //     } catch (\Exception $e) {
    //         return redirect()->route('home');
    //     }
    // }

    public function success(Request $request)
{
    try {
        $token = Session::get('jwt_token');
        if (!$token) {
            throw new \Exception('Token not found in session');
        }

        $newRequest = new Request();
        $newRequest->merge([
            'order_id' => $request->order_id
        ]);
        $newRequest->query->set('token', $token);

        $paymentController = new ApiPaymentController();
        $response = $paymentController->finishPayment($newRequest);

        if (!$response) {
            throw new \Exception('Payment finishing failed');
        }

        return view('payment.payment-success');
    } catch (\Exception $e) {
        Log::error('Payment success error: ' . $e->getMessage());
        return redirect()->route('home')->with('error', 'Payment processing failed');
    }
}
}
