<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->generateResponse('error', $validator->errors(), null, 400);
        }

        $customer = Customer::where('username', $request->username)->first();
        if (!$customer) {
            return $this->generateResponse('error', 'Invalid username or password', null, 401);
        }

        if (!Hash::check($request->password, $customer->password)) {
            return $this->generateResponse('error', 'Invalid username or password', null, 401);
        }

        $token = JWTAuth::fromUser($customer);
        if (!$token) {
            return $this->generateResponse('error', 'Unauthorized', null, 401);
        } else {
            return $this->generateResponse('success', 'Login success', ['token' => $token], 200);
        }
    }


}
