<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BillingRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $billing = BillingRevenue::with(['order', 'order.customer', 'order.layanan', 'kontrak_nodelink', 'kontrak_nodelink.nodelink'])
                ->select('tbl_billing_revenue.*')->whereHas('order', function ($query) {
                    $query->whereNotNull(('nama_node'));
                });

            return DataTables::of($billing)
                ->addColumn('action', function ($billing) {
                    $action = '<a href="javascript:void(0);" class="btn btn-primary btn-sm view-billing-btn" data-id="' . $billing->id . '">
                    <i class="bi bi-eye"></i></a>';

                    if ($billing->bukti_ppn != null) {
                        if ($billing->is_lunas == 0 && $billing->is_reject == 0) {
                            $action .= '
                            <button class="btn btn-success btn-sm terima-btn" data-id="' . $billing->id . '"><i class="bi bi-check-circle"></i></button>
                            <button class="btn btn-danger btn-sm tolak-btn" data-id="' . $billing->id . '"><i class="bi bi-x-circle"></i></button>
                        ';
                        }
                    }

                    return $action;
                })
                ->addIndexColumn()
                ->addColumn('unique_order', function ($billing) {
                    return $billing->order->unique_order;
                })
                ->addColumn('tanggal_tagih', function ($billing) {
                    return $billing->tanggal_tagih;
                })
                ->addColumn('customer', function ($billing) {
                    return $billing->order->customer->nama_perusahaan;
                })
                ->addColumn('total_akhir', function ($billing) {
                    return 'Rp ' . number_format($billing->total_akhir, 0, ',', '.');
                })
                ->addColumn('status', function ($billing) {
                    $badgeClass = 'secondary';
                    $statusText = 'Tidak Diketahui';

                    if ($billing->status == "Unpaid") {
                        $badgeClass = 'danger';
                        $statusText = 'Belum Dibayar';
                    } elseif ($billing->status == "Paid" && $billing->is_lunas == 0) {
                        $badgeClass = 'warning';
                        $statusText = 'Belum Lunas';
                    } elseif ($billing->status == "Paid" && $billing->is_lunas == 1) {
                        $badgeClass = 'success';
                        $statusText = 'Lunas';
                    } elseif ($billing->status == "Paid") {
                        $badgeClass = 'primary';
                        $statusText = 'Dibayar';
                    }

                    return '<span class="badge bg-' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('jatuh_tempo', function ($billing) {
                    return $billing->jatuh_tempo;
                })
                ->addColumn('bukti_ppn', function ($billing) {
                    if ($billing->bukti_ppn) {
                        $url = url('/api/ppn-image/' . $billing->id);
                        return '<a href="' . $url . '" target="_blank" class="btn btn-info btn-sm">Preview</a>';
                    }
                    return '-';
                })
                ->rawColumns(['action', 'customer', 'status', 'total_akhir', 'jatuh_tempo', 'bukti_ppn'])
                ->filterColumn('unique_order', function ($query, $keyword) {
                    $query->whereHas('order', function ($q) use ($keyword) {
                        $q->where('unique_order', 'like', "%$keyword%");
                    });
                })
                ->orderColumn('unique_order', function ($query, $order) {
                    $query->join('tbl_order', 'tbl_order.id', '=', 'tbl_billing_revenue.order_id')
                        ->orderBy('tbl_order.unique_order', $order);
                })
                ->filterColumn('customer', function ($query, $keyword) {
                    $query->whereHas('order.customer', function ($q) use ($keyword) {
                        $q->where('nama_perusahaan', 'like', "%$keyword%");
                    });
                })
                ->orderColumn('customer', function ($query, $order) {
                    $query->join('tbl_order', 'tbl_order.id', '=', 'tbl_billing_revenue.order_id')
                        ->join('tbl_customer', 'tbl_customer.id', '=', 'tbl_order.customer_id')
                        ->orderBy('tbl_customer.nama_perusahaan', $order);
                })
                ->filterColumn('total_akhir', function ($query, $keyword) {
                    $query->where('total_akhir', 'like', "%$keyword%");
                })
                ->orderColumn('total_akhir', function ($query, $order) {
                    $query->orderBy('total_akhir', $order);
                })
                ->filterColumn('status_text', function ($query, $keyword) {
                    $query->whereRaw("
                    CASE
                        WHEN status = 'Unpaid' THEN 'Belum Dibayar'
                        WHEN status = 'Paid' AND is_lunas = 0 THEN 'Belum Lunas'
                        WHEN status = 'Paid' AND is_lunas = 1 THEN 'Lunas'
                        ELSE 'Dibayar'
                    END LIKE ?", ["%$keyword%"]);
                })
                ->orderColumn('status_text', function ($query, $order) {
                    $query->orderByRaw("
                    CASE
                        WHEN status = 'Unpaid' THEN 1
                        WHEN status = 'Paid' AND is_lunas = 0 THEN 2
                        WHEN status = 'Paid' AND is_lunas = 1 THEN 3
                        ELSE 4
                    END $order");
                })
                ->filterColumn('jatuh_tempo', function ($query, $keyword) {
                    $query->where('jatuh_tempo', 'like', "%$keyword%");
                })
                ->orderColumn('jatuh_tempo', function ($query, $order) {
                    $query->orderBy('jatuh_tempo', $order);
                })
                ->make(true);
        }
        return view("admin.pages.billing.index");
    }

    public function show($id)
    {
        $billing = BillingRevenue::with(['order', 'order.customer', 'order.layanan', 'kontrak_nodelink', 'kontrak_nodelink.nodelink'])
            ->findOrFail($id);

        return response()->json([
            'id' => $billing->id,
            'customer' => $billing->order->customer->nama_perusahaan,
            'tanggal_tagih' => $billing->tanggal_tagih,
            'jatuh_tempo' => $billing->jatuh_tempo,
            'tanggal_pembayaran' => $billing->tanggal_pembayaran ?? 'Belum Dibayar',
            'layanan' => $billing->order->layanan->nama_layanan,
            'sid' => $billing->kontrak_nodelink->nodelink->sid ?? '-',
            'status' => $billing->status == "Unpaid" ? 'Belum Dibayar' : ($billing->status == "Paid" && is_null($billing->bukti_ppn) ? 'Belum Lunas' : ($billing->status == "Paid" && !is_null($billing->bukti_ppn) ? 'Lunas' : 'Dibayar')),
            'total' => 'Rp ' . number_format($billing->total_akhir, 0, ',', '.')
        ]);
    }

    public function generateBilling()
    {
        try {
            DB::beginTransaction();

            $monthOldNodelinks = BillingRevenue::with(['order', 'order.proforma_invoice'])->where('status', 'Paid')
                ->where('jatuh_tempo', '<=', Carbon::now()->endOfMonth())
                ->whereNotNull('bukti_ppn')
                ->whereHas('order', function ($query) {
                    $query->whereNotNull(('nama_node'));
                })
                ->get();

            $count = 0;

            foreach ($monthOldNodelinks as $nodelink) {
                if ($this->createBillingIfNotExists($nodelink)) {
                    $count++;
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil membuat {$count} billing revenue baru"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat membuat billing revenue: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat generate billing'
            ], 500);
        }
    }

    private function createBillingIfNotExists($nodelink)
    {
        $maxLoop = 24;
        $bulanCek = Carbon::now()->startOfMonth();

        for ($i = 0; $i < $maxLoop; $i++) {
            $exists = BillingRevenue::where('order_id', $nodelink->order_id)
                ->whereDate('tanggal_tagih', $bulanCek)
                ->exists();

            if (!$exists) {
                $deposit = $nodelink->order->proforma_invoice->value('deposit_layanan');
                $ppn = round($deposit * 0.11);
                $totalAkhir = $deposit;

                BillingRevenue::create([
                    'kontrak_nodelink_id' => $nodelink->id,
                    'order_id' => $nodelink->order_id,
                    'tanggal_tagih' => $bulanCek,
                    'total_tagihan' => $deposit,
                    'total_ppn' => $ppn,
                    'total_akhir' => $totalAkhir,
                    'jatuh_tempo' => $bulanCek->copy()->endOfMonth(),
                    'status' => 'Unpaid'
                ]);

                return true;
            }

            $bulanCek->addMonthNoOverflow();
        }

        return false;
    }
}
