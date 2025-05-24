<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillingRevenue;
use App\Models\KontrakNodelink;
use App\Models\Nodelink;
use App\Models\Popup;
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

    // public static function generateBillingRevenue()
    // {
    //     $oneMonthOldNodelinks = KontrakNodelink::query()
    //         ->whereRaw('DATE(created_date) = ?', [Carbon::now()->subMonth()->format('Y-m-d')])
    //         ->whereNotExists(function ($query) {
    //             $query->select(DB::raw(1))
    //                 ->from('tbl_billing_revenue')
    //                 ->whereRaw('tbl_billing_revenue.kontrak_nodelink_id = tbl_kontrak_nodelink.id');
    //         })
    //         ->with(['kontrak_layanan.kontrak.order'])
    //         ->get();

    //     $lastMonth = Carbon::now()->subMonth();
    //     $billingStartDate = Carbon::parse($lastMonth)->startOfMonth();
    //     $billingDueDate = Carbon::parse($lastMonth)->endOfMonth();

    //     foreach ($oneMonthOldNodelinks as $nodelink) {
    //         $ppn = ceil($nodelink->total_biaya * 0.11);

    //         BillingRevenue::create([
    //             'kontrak_nodelink_id' => $nodelink->id,
    //             'order_id' => $nodelink->kontrak_layanan->kontrak->order->id,
    //             'tanggal_tagih' => $billingStartDate,
    //             'total_tagihan' => $nodelink->total_biaya,
    //             'total_ppn' => $ppn,
    //             'total_akhir' => $nodelink->total_biaya + $ppn,
    //             'jatuh_tempo' => $billingDueDate,
    //             'status' => 'Unpaid'
    //         ]);
    //     }
    // }

    public function billingSummary(Request $request)
    {
        try {
            if (!JWTAuth::getToken()) {
                return $this->generateResponse('error', 'Token not provided', null, 401);
            }

            $user = JWTAuth::parseToken()->authenticate();

            $query = BillingRevenue::select([
                'tbl_billing_revenue.id as billing_id',
                'tbl_billing_revenue.order_id',
                'tbl_order.sid as order_sid',
                'tbl_order.unique_order as order_unique',
                'tbl_order.nama_node',
                'tbl_billing_revenue.total_akhir as nominal',
                'tbl_billing_revenue.jatuh_tempo',
                'tbl_billing_revenue.status',
                'tbl_billing_revenue.bukti_ppn',
                'tbl_billing_revenue.is_lunas',
            ])
                ->join('tbl_order', 'tbl_billing_revenue.order_id', '=', 'tbl_order.id')
                ->where('tbl_order.customer_id', $user->id)
                ->whereNotNull('tbl_order.nama_node')
                ->orderBy('tbl_billing_revenue.jatuh_tempo', 'desc');

            if ($request->filled('nama_node')) {
                $query->where('tbl_order.nama_node', 'LIKE', "%{$request->nama_node}%");
            }

            if ($request->filled('pembayaran')) {
                $status = strtolower($request->pembayaran);
                if (in_array($status, ['unpaid', 'paid'])) {
                    $query->where('tbl_billing_revenue.status', ucfirst($status));
                }
            }

            if ($request->filled('pelunasan')) {
                if ($request->pelunasan === 'paid') {
                    $query->where('tbl_billing_revenue.is_lunas', 1);
                } elseif ($request->pelunasan === 'unpaid') {
                    $query->where('tbl_billing_revenue.is_lunas', 0);
                }
            }

            if ($request->filled('simply') && $request->simply !== 'all') {
                [$month, $year] = explode('-', $request->simply);
                $month = (int) $month;
                $year = (int) $year;

                $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
                $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

                $query->whereBetween('tbl_billing_revenue.jatuh_tempo', [$startDate, $endDate]);
            }

            if ($request->filled('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $numericSearch = preg_replace('/[^\d]/', '', $search);
                    $alphanumericSearch = preg_replace('/[^a-zA-Z0-9\-]/', '', $search);

                    $q->whereRaw('LOWER(tbl_order.unique_order) LIKE ?', ['%' . strtolower($alphanumericSearch) . '%'])
                        ->orWhere('tbl_billing_revenue.total_akhir', 'like', "%{$numericSearch}%");
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

    public function nearby()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $billing = BillingRevenue::select([
                'tbl_billing_revenue.id as billing_id',
                'tbl_billing_revenue.order_id',
                'tbl_order.sid as order_sid',
                'tbl_order.unique_order as order_unique',
                'tbl_order.nama_node',
                'tbl_order.alamat_node',
                'tbl_billing_revenue.total_akhir as nominal',
                'tbl_billing_revenue.jatuh_tempo',
                'tbl_billing_revenue.status',
                'tbl_billing_revenue.bukti_ppn as bukti_ppn'
            ])
                ->join('tbl_order', 'tbl_billing_revenue.order_id', '=', 'tbl_order.id')
                ->where('tbl_order.customer_id', $user->id)
                ->where('tbl_billing_revenue.status', 'Unpaid')
                ->orWhere('tbl_billing_revenue.bukti_ppn', null)
                ->orderBy('tbl_billing_revenue.jatuh_tempo', 'asc')
                ->where('tbl_order.nama_node', '!=', null)
                ->limit(1)->get();

            $data = [];
            foreach ($billing as $bill) {
                $nodelink = Nodelink::whereHas('kontrak_nodelink.kontrak_layanan.kontrak', function ($query) use ($bill) {
                    $query->where('order_id', $bill->order_id);
                })
                    ->first();

                $imagePath = public_path('assets/images/' . $bill->bukti_ppn);
                $bukti_ppn = file_exists($imagePath) && $bill->bukti_ppn
                    ? url('assets/images/' . $bill->bukti_ppn)
                    : null;

                $data[] = [
                    'billing_id' => $bill->billing_id,
                    'order_id' => $bill->order_id,
                    'order_sid' => $bill->order_sid,
                    'order_unique' => $bill->order_unique,
                    'nama_node' => $nodelink->nama_node,
                    'alamat_node' => $nodelink->alamat_node,
                    'nominal' => $bill->nominal,
                    'jatuh_tempo' => $bill->jatuh_tempo,
                    'status' => $bill->status,
                    'recurring' => 'Ya',
                    'latitude' => $nodelink->latitude,
                    'longitude' => $nodelink->longitude,
                    'bukti_ppn' => $bukti_ppn
                ];
            }

            return $this->generateResponse('success', 'Data billing berhasil diambil', $data);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
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
                'tbl_order.nama_node',
                'tbl_order.alamat_node',
                'tbl_billing_revenue.total_akhir as nominal',
                'tbl_billing_revenue.jatuh_tempo',
                'tbl_billing_revenue.status',
                'tbl_billing_revenue.bukti_ppn as bukti_ppn',
                'tbl_billing_revenue.is_clicked',
                'tbl_billing_revenue.payment_url',
                'tbl_billing_revenue.is_lunas',
                'tbl_billing_revenue.is_reject',
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

            $imagePath = storage_path('app/public/' . $billing->bukti_ppn);
            $bukti_ppn = file_exists($imagePath) && $billing->bukti_ppn
                ? url('/api/ppn-image/' . $billing->billing_id)
                : null;

            $popup = Popup::where('customer_id', $user->id)
                ->whereDate('created_at', Carbon::today())->where('id_order', null)
                ->first();

            $data = [
                'billing_id' => $billing->billing_id,
                'order_id' => $billing->order_id,
                'order_sid' => $billing->order_sid,
                'order_unique' => $billing->order_unique,
                'nama_node' => $nodelink->nama_node,
                'alamat_node' => $nodelink->alamat_node,
                'nominal' => $billing->nominal,
                'jatuh_tempo' => $billing->jatuh_tempo,
                'status' => $billing->status,
                'recurring' => 'Ya',
                'latitude' => $nodelink->latitude,
                'longitude' => $nodelink->longitude,
                'no_invoice' => $invoice->no_proforma_invoice,
                'tanggal_invoice' => $invoice->tanggal_proforma,
                'bukti_ppn' => $bukti_ppn,
                'popup' => $popup ? 1 : 0,
                'is_clicked' => $billing->is_clicked,
                'payment_url' => $billing->payment_url,
                'is_lunas' => $billing->is_lunas,
                'is_reject' => $billing->is_reject,
            ];

            return $this->generateResponse('success', 'Data billing berhasil diambil', $data);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->generateResponse('error', 'User tidak ditemukan');
            }

            $billing_id = $request->billing_id;
            $bukti_ppn = $request->file('bukti_ppn');

            $request->validate([
                'bukti_ppn' => 'required|file|mimes:pdf|max:2048',
            ]);

            DB::beginTransaction();

            $billing = BillingRevenue::find($billing_id);
            if (!$billing) {
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            $imagePath = storage_path('app/public/');
            $imageName = 'bukti_ppn_' . $billing_id . '.' . $bukti_ppn->getClientOriginalExtension();
            $bukti_ppn->move($imagePath, $imageName);

            $billing->bukti_ppn = $imageName;
            $billing->is_reject = 0;
            $billing->save();
            DB::commit();

            return $this->generateResponse('success', 'Bukti PPN berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function monthBilling()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->generateResponse('error', 'User tidak ditemukan');
            }

            $billings = BillingRevenue::select([
                'tbl_billing_revenue.id as billing_id',
                'tbl_billing_revenue.order_id',
                'tbl_billing_revenue.jatuh_tempo',
            ])
                ->join('tbl_order', 'tbl_billing_revenue.order_id', '=', 'tbl_order.id')
                ->where('tbl_order.customer_id', $user->id)
                ->get();

            $periode = collect($billings)->map(function ($billing) {
                $bulanIndonesia = [
                    'January' => 'Januari',
                    'February' => 'Februari',
                    'March' => 'Maret',
                    'April' => 'April',
                    'May' => 'Mei',
                    'June' => 'Juni',
                    'July' => 'Juli',
                    'August' => 'Agustus',
                    'September' => 'September',
                    'October' => 'Oktober',
                    'November' => 'November',
                    'December' => 'Desember',
                ];

                $bulan = Carbon::parse($billing->jatuh_tempo)->format('F');
                $tahun = Carbon::parse($billing->jatuh_tempo)->format('Y');

                return [
                    'periode' => $bulanIndonesia[$bulan] . ' ' . $tahun,
                    'simply' => Carbon::parse($billing->jatuh_tempo)->format('m-Y')
                ];
            })->unique()->values()->toArray();

            array_unshift($periode, [
                'periode' => 'All',
                'simply' => 'all'
            ]);

            return $this->generateResponse('success', 'Data periode billing berhasil diambil', $periode);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function ppnImage($billingId)
    {
        try {
            $billing = BillingRevenue::find($billingId);
            if (!$billing) {
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            $imagePath = storage_path('app/public/' . $billing->bukti_ppn);
            if (!file_exists($imagePath)) {
                return $this->generateResponse('error', 'Bukti PPN tidak ditemukan');
            }

            return response()->file($imagePath);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function ppnImageAdmin($billingId)
    {
        try {
            $billing = BillingRevenue::find($billingId);
            if (!$billing) {
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            $imagePath = storage_path('app/public/' . $billing->bukti_ppn);
            if (!file_exists($imagePath)) {
                return $this->generateResponse('error', 'Bukti PPN tidak ditemukan');
            }

            return response()->file($imagePath);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function acceptPpn(Request $request)
    {
        try {
            $billing = BillingRevenue::find($request->billing_id);
            if (!$billing) {
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            DB::beginTransaction();
            $billing->is_lunas = 1;
            $billing->save();
            DB::commit();

            return $this->generateResponse('success', 'Bukti PPN diterima, pembayaran lunas');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage());
        }
    }

    public function rejectPpn(Request $request)
    {
        try {
            $billing = BillingRevenue::find($request->billing_id);
            if (!$billing) {
                return $this->generateResponse('error', 'Billing tidak ditemukan');
            }

            DB::beginTransaction();
            $billing->is_lunas = 0;
            $billing->is_reject = 1;
            $billing->save();
            DB::commit();

            return $this->generateResponse('success', 'Bukti PPN ditolak, pembayaran belum lunas');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->generateResponse('error', $e->getMessage());
        }
    }
}
