<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RiwayatDeposit;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class DepositController extends Controller
{
    private function generateResponse($status, $message, $data = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function active(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $deposit = RiwayatDeposit::getDepositAktif($user->id);
            return $this->generateResponse('success', 'Data Riwayat Deposit', $deposit);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function used(){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $deposit = RiwayatDeposit::getDepositUsed($user->id);
            return $this->generateResponse('success', 'Data Riwayat Deposit', $deposit);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function summary(){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $total_deposit_aktif = RiwayatDeposit::where('tipe', 'Aktif')
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('customer_id', $user->id);
                })->sum('jumlah');

            $total_deposit_terpakai = RiwayatDeposit::where('tipe', 'Terpakai')
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('customer_id', $user->id);
                })->sum('jumlah');

            $data = [
                'total_deposit_aktif' => $total_deposit_aktif ?? 0,
                'total_deposit_terpakai' => $total_deposit_terpakai ?? 0,
            ];

            return $this->generateResponse('success', 'Summary Deposit', $data);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }
}
