<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        try {
            $token = Session::get('jwt_token');

            $response = new ApiPaymentController();
            $response = $response->finishPayment($token);

            return view('payment.payment-success', ['token' => $token, 'response' => $response]);
        } catch (\Exception $e) {
            return redirect()->route('home');
        }
    }
}
