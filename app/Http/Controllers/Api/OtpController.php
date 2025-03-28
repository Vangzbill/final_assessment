<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function verifyOtp(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email_perusahaan' => 'required|email|exists:tbl_customer,email_perusahaan',
                'otp_code' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->generateResponse('error', $validator->errors()->first(), null, 422);
            }

            $customer = Customer::where('email_perusahaan', $request->email_perusahaan)
                ->where('otp_code', $request->otp_code)
                ->first();

            if (!$customer) {
                return $this->generateResponse('error', 'OTP is incorrect', null, 400);
            }

            $customer->email_verified_at = now();
            $customer->otp_code = null;
            $customer->save();

            return $this->generateResponse('success', 'Email verified successfully', $customer, 200);
        }
        catch(Exception $e){
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function send(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email_perusahaan' => 'required|email|exists:tbl_customer,email_perusahaan',
            ]);

            if ($validator->fails()) {
                return $this->generateResponse('error', $validator->errors()->first(), null, 422);
            }

            $customer = Customer::where('email_perusahaan', $request->email_perusahaan)->first();
            if (!$customer) {
                return $this->generateResponse('error', 'Email not found', null, 404);
            }

            $otp_code = rand(100000, 999999);
            $customer->otp_code = $otp_code;
            $customer->save();

            Mail::to($customer->email_perusahaan)->send(new SendOtpMail($otp_code));

            return $this->generateResponse('success', 'OTP sent successfully', $customer, 200);
        }
        catch(Exception $e){
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
