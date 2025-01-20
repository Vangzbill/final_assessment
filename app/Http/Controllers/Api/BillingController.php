<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingRevenue;
use App\Models\KontrakNodelink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class BillingController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function generateBillingRevenue()
    {
        $oneMonthOldNodelinks = KontrakNodelink::query()
            ->whereRaw('DATE(created_date) = ?', [Carbon::now()->subMonth()->format('Y-m-d')])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tbl_billing_revenue')
                    ->whereRaw('tbl_billing_revenue.kontrak_nodelink_id = tbl_kontrak_nodelink.id');
            })
            ->with(['kontrak_layanan.kontrak.order'])
            ->get();

        $lastMonth = Carbon::now()->subMonth();
        $billingStartDate = Carbon::parse($lastMonth)->startOfMonth();
        $billingDueDate = Carbon::parse($lastMonth)->endOfMonth();

        foreach ($oneMonthOldNodelinks as $nodelink) {
            $ppn = ceil($nodelink->total_biaya * 0.11);

            BillingRevenue::create([
                'kontrak_nodelink_id' => $nodelink->id,
                'order_id' => $nodelink->kontrak_layanan->kontrak->order->id,
                'tanggal_tagih' => $billingStartDate,
                'total_tagihan' => $nodelink->total_biaya,
                'total_ppn' => $ppn,
                'total_akhir' => $nodelink->total_biaya + $ppn,
                'jatuh_tempo' => $billingDueDate,
                'status' => 'PENDING'
            ]);
        }
    }

    public function billingSummary()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $billings = BillingRevenue::select([
                'tbl_billing_revenue.id as billing_id',
                'tbl_billing_revenue.order_id',
                'tbl_order.sid as order_sid',
                'tbl_order.unique_order as order_unique',
                'tbl_billing_revenue.total_akhir as nominal',
                'tbl_billing_revenue.jatuh_tempo'
            ])
                ->join('tbl_order', 'tbl_billing_revenue.order_id', '=', 'tbl_order.id')
                ->where('tbl_order.customer_id', $user->id)
                ->orderBy('tbl_billing_revenue.jatuh_tempo', 'desc')
                ->get();

            return $this->generateResponse('success', 'Data billing berhasil diambil', $billings);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }
}
