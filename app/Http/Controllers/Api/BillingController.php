<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingRevenue;
use App\Models\KontrakNodelink;
use App\Models\Nodelink;
use App\Models\ProformaInvoice;
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

    public function billingSummary(Request $request)
    {
        try {
            if (!$token = JWTAuth::getToken()) {
                return $this->generateResponse('error', 'Token not provided', null, 401);
            }

            $user = JWTAuth::parseToken()->authenticate();

            $query = BillingRevenue::select([
                'tbl_billing_revenue.id as billing_id',
                'tbl_billing_revenue.order_id',
                'tbl_order.sid as order_sid',
                'tbl_order.unique_order as order_unique',
                'tbl_billing_revenue.total_akhir as nominal',
                'tbl_billing_revenue.jatuh_tempo',
                'tbl_billing_revenue.status',
                'tbl_billing_revenue.bukti_ppn'
            ])
                ->join('tbl_order', 'tbl_billing_revenue.order_id', '=', 'tbl_order.id')
                ->where('tbl_order.customer_id', $user->id)
                ->orderBy('tbl_billing_revenue.jatuh_tempo', 'desc');

            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(tbl_order.unique_order) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhere('tbl_billing_revenue.total_akhir', 'like', '%' . $search . '%');
                });
            }

            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $billings = $query->paginate($perPage, ['*'], 'page', $page);

            $pagination = [
                'current_page' => $billings->currentPage(),
                'total_pages' => $billings->lastPage(),
                'total_items' => $billings->total(),
                'per_page' => $billings->perPage(),
            ];

            return $this->generateResponse('success', 'Data billing berhasil diambil', [
                'billings' => $billings->items(),
                'pagination' => $pagination,
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->generateResponse('error', 'Token has expired', null, 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->generateResponse('error', 'Token is invalid', null, 401);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }


    public function billingDetail($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $billing = BillingRevenue::select([
                'tbl_billing_revenue.id as billing_id',
                'tbl_billing_revenue.order_id',
                'tbl_order.sid as order_sid',
                'tbl_order.unique_order as order_unique',
                'tbl_billing_revenue.total_akhir as nominal',
                'tbl_billing_revenue.jatuh_tempo',
                'tbl_billing_revenue.status',
                'tbl_billing_revenue.bukti_ppn as bukti_ppn'
            ])
                ->join('tbl_order', 'tbl_billing_revenue.order_id', '=', 'tbl_order.id')
                ->where('tbl_order.customer_id', $user->id)
                ->where('tbl_billing_revenue.id', $id)
                ->first();

            if (!$billing) {
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            $orderId = $billing->order_id;
            $nodelink = Nodelink::whereHas('kontrak_nodelink.kontrak_layanan.kontrak', function ($query) use ($orderId) {
                $query->where('order_id', $orderId);
            })
                ->first();

            $invoice = ProformaInvoice::where('order_id', $orderId)->first();

            $imagePath = public_path('assets/images/' . $billing->bukti_ppn);
            $bukti_ppn = file_exists($imagePath) && $billing->bukti_ppn
                ? url('assets/images/' . $billing->bukti_ppn)
                : '';

            $data = [
                'billing_id' => $billing->billing_id,
                'order_id' => $billing->order_id,
                'order_sid' => $billing->order_sid,
                'order_unique' => $billing->order_unique,
                'nominal' => $billing->nominal,
                'jatuh_tempo' => $billing->jatuh_tempo,
                'status' => $billing->status,
                'recurring' => 'Ya',
                'nama_node' => $nodelink->nama_node,
                'latitude' => $nodelink->latitude,
                'longitude' => $nodelink->longitude,
                'no_invoice' => $invoice->no_proforma_invoice,
                'tanggal_invoice' => $invoice->tanggal_proforma,
                'bukti_ppn' => $bukti_ppn
            ];

            return $this->generateResponse('success', 'Data billing berhasil diambil', $data);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function upload(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return $this->generateResponse('error', 'User tidak ditemukan');
            }

            $billing_id = $request->billing_id;
            $bukti_ppn = $request->file('bukti_ppn');

            DB::beginTransaction();

            $billing = BillingRevenue::find($billing_id);
            if(!$billing){
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            $imagePath = public_path('assets/images/');
            $imageName = 'bukti_ppn_' . $billing_id . '.' . $bukti_ppn->getClientOriginalExtension();
            $bukti_ppn->move($imagePath, $imageName);

            $billing->bukti_ppn = $imageName;
            $billing->save();
            DB::commit();

            return $this->generateResponse('success', 'Bukti PPN berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage());
        }
    }
}
