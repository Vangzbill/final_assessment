<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nps;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class NpsController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function createNps(Request $request)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();

            DB::beginTransaction();
            $nps = new Nps();
            $nps->customer_id = $user->id;
            $nps->nama_customer = $user->nama_perusahaan;
            $nps->rating = $request->rating;
            $nps->feedback = $request->feedback;
            $nps->created_date = Carbon::now();
            $nps->save();
            DB::commit();

            return $this->generateResponse('success', 'NPS created', $nps, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
