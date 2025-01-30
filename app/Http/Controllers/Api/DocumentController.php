<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingRevenue;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class DocumentController extends Controller
{
    private function generateResponse($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function invoice($id)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrder($id, $user->id);
            if(!$order){
                return $this->generateResponse('error', 'Order not found', null, 404);
            }

            $pdf = Pdf::loadView('document.invoice', ['order' => $order]);
            return $pdf->download('invoice-'.$order['order_id'].'.pdf');
        }catch(\Exception $e){
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function acceptanceLetter($id)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getOrder($id, $user->id);
            if(!$order){
                return $this->generateResponse('error', 'Order not found', null, 404);
            }

            $pdf = Pdf::loadView('document.acceptance-letter', ['order' => $order]);
            return $pdf->download('acceptance-letter-'.$order['unique_order'].'.pdf');
        }catch(\Exception $e){
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function activationLetter($id)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::getActivation($id, $user->id);
            if(!$order){
                return $this->generateResponse('error', 'Order not found', null, 404);
            }

            $pdf = Pdf::loadView('document.activation-letter', ['order' => $order]);
            return $pdf->download('activation-letter-'.$order['unique_order'].'.pdf');
        }catch(\Exception $e){
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function signature(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $order = Order::where('id', $request->order_id)->where('user_id', $user->id)->first();
            if(!$order){
                return $this->generateResponse('error', 'Order not found', null, 404);
            }

            DB::beginTransaction();
            $order->is_ttd = 1;
            $order->save();
            DB::commit();

            return $this->generateResponse('success', 'Signature saved', null, 200);
        }catch(\Exception $e){
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function billingInvoice($id)
    {
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $order = BillingRevenue::getBilling($id, $user->id);
            if(!$order){
                return $this->generateResponse('error', 'Order not found', null, 404);
            }

            $pdf = Pdf::loadView('document.billing-invoice', ['order' => $order]);
            return $pdf->download('billing-invoice-'.$order['no_proforma_invoice'].'.pdf');
        }catch(\Exception $e){
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }
}
