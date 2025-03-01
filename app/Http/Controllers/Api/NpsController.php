<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nps;
use App\Models\Popup;
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
            $nps->jenis = $request->jenis;
            $nps->save();
            DB::commit();

            return $this->generateResponse('success', 'NPS created', $nps, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function popup(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $existingPopupToday = Popup::where('customer_id', $user->id)
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->first();

            if(!$existingPopupToday || $request->id_order != null){
                DB::beginTransaction();
                $popup = new Popup();
                $popup->customer_id = $user->id;
                $popup->id_order = $request->id_order;
                $popup->created_at = Carbon::today()->toDateString();
                $popup->save();
                DB::commit();
                return $this->generateResponse('success', 'Popup created', $popup, 200);
            } else {
                return $this->generateResponse('error', 'Popup already created today', null, 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
