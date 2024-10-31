<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        $otp = Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => $expiresAt
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'data' => $otp
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $otp = Otp::valid($request->email, $request->otp)->first();

        if ($otp) {
            $otp->update(['is_verified' => true]);
            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP'
            ], 400);
        }
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('expires_at', '>=', now())
            ->where('is_verified', false)
            ->first();

        if ($otp) {
            return response()->json([
                'status' => 'error',
                'message' => 'OTP still valid'
            ], 400);
        }

        $otp = Otp::create([
            'email' => $request->email,
            'otp' => rand(100000, 999999),
            'expires_at' => now()->addMinutes(5)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'data' => $otp
        ]);
    }

    public function check(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $otp = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if ($otp) {
            return response()->json([
                'status' => 'success',
                'message' => 'OTP valid'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP'
            ], 400);
        }
    }

}
