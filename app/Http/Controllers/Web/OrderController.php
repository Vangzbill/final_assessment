<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    private function generateResponse($status, $message, $data = null, $code = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['produk', 'layanan', 'order_status_history.status', 'cp_customer', 'customer'])
                ->select('tbl_order.*');

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('unique_order', function ($order) {
                    return $order->unique_order;
                })
                ->addColumn('order_date', function ($order) {
                    return Carbon::parse($order->order_date)->format('d-m-Y');
                })
                ->addColumn('customer', function ($order) {
                    return '<strong>' . $order->customer->nama_perusahaan . '</strong><br>' . $order->customer->alamat;
                })
                ->addColumn('pic', function ($order) {
                    return $order->cp_customer->nama ?? '-';
                })
                ->addColumn('produk', function ($order) {
                    return '<strong>' . $order->produk->nama_produk . '</strong><br>' . $order->layanan->nama_layanan;
                })
                ->addColumn('jenis_pengiriman', function ($order) {
                    $pengiriman = $order->jenis_pengiriman;
                    if ($pengiriman == 'JNE') {
                        return '<strong>' . $pengiriman . '</strong><br>' . $order->provinsi . ', ' . $order->kabupaten . ', ' . $order->alamat_lengkap;
                    }
                    return $pengiriman;
                })
                ->addColumn('total', function ($order) {
                    return 'Rp ' . number_format($order->total_harga, 0, ',', '.');
                })
                ->addColumn('status', function ($order) {
                    $status = $order->order_status_history->last()->status->nama_status_order;
                    $warna = match ($status) {
                        'Order Diterima' => 'primary',
                        'Pembayaran' => 'info',
                        'Pengiriman' => 'success',
                        'Pesanan Diterima' => 'success',
                        'Aktivasi Layanan' => 'success',
                        'Surat Pernyataan Aktivasi' => 'success',
                        'Pesanan Selesai' => 'success',
                        'Pesanan Dibatalkan' => 'danger',
                        'Alamat Layanan' => 'warning',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $warna . '">' . $status . '</span>';
                })
                ->addColumn('action', function ($order) {
                    if ($order->order_status_history->last()->status->id == 2) {
                        return '<a href="javascript:void(0);" class="btn btn-primary btn-sm view-order-btn" data-id="' . $order->id . '">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="javascript:void(0);" class="btn btn-warning btn-sm update-status-btn" data-id="' . $order->id . '">
                            <i class="bi bi-pencil-square"></i>
                        </a>';
                    } else {
                        return '<a href="javascript:void(0);" class="btn btn-primary btn-sm view-order-btn" data-id="' . $order->id . '">
                            <i class="bi bi-eye"></i>
                        </a>';
                    }
                })
                ->rawColumns(['unique_order', 'order_date', 'customer', 'produk', 'jenis_pengiriman', 'status', 'action'])
                ->filter(function ($query) use ($request) {
                    if (!empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where('unique_order', 'LIKE', "%{$search}%")
                            ->orWhereHas('customer', function ($q) use ($search) {
                                $q->where('nama_perusahaan', 'LIKE', "%{$search}%");
                            })
                            ->orWhereHas('cp_customer', function ($q) use ($search) {
                                $q->where('nama', 'LIKE', "%{$search}%");
                            })
                            ->orWhereHas('produk', function ($q) use ($search) {
                                $q->where('nama_produk', 'LIKE', "%{$search}%");
                            })
                            ->orWhereHas('layanan', function ($q) use ($search) {
                                $q->where('nama_layanan', 'LIKE', "%{$search}%");
                            })
                            ->orWhere('jenis_pengiriman', 'LIKE', "%{$search}%")
                            ->orWhere('total_harga', 'LIKE', "%{$search}%")
                            ->orWhereHas('order_status_history', function ($q) use ($search) {
                                $q->whereHas('status', function ($q2) use ($search) {
                                    $q2->where('nama_status_order', 'LIKE', "%{$search}%");
                                });
                            });
                    }
                })
                ->make(true);
        }
        return view('admin.pages.order.index');
    }

    public function updateStatus($id)
    {
        try {
            $order = Order::find($id);
            DB::beginTransaction();
            $orderHistory = new OrderStatusHistory();
            $orderHistory->order_id = $order->id;
            $orderHistory->status_id = 3;
            $orderHistory->keterangan = 'Pesanan sedang dikirim';
            $orderHistory->tanggal = now();
            $orderHistory->save();

            $order->riwayat_status_order_id = $orderHistory->id;
            $order->save();
            DB::commit();

            return $this->generateResponse('success', 'Status pesanan berhasil diubah', null, 200);
        } catch (\Exception $e) {
            return $this->generateResponse('error', $e->getMessage(), null, 500);
        }
    }

    public function show($id)
    {
        $order = Order::with(['produk', 'customer', 'order_status_history.status'])
            ->where('id', $id)
            ->first();

        if (!$order) {
            return $this->generateResponse('error', 'Pesanan tidak ditemukan', null, 404);
        }

        $statusList = $order->order_status_history->map(function ($history) {
            return [
                'nama_status' => $history->status->nama_status_order,
                'tanggal' => Carbon::parse($history->tanggal)->format('d-m-Y H:i'),
                'keterangan' => $history->keterangan ?? '-',
            ];
        });

        $response = [
            'unique_order' => $order->unique_order,
            'order_date' => Carbon::parse($order->order_date)->format('d-m-Y'),
            'customer' => [
                'nama_perusahaan' => $order->customer->nama_perusahaan,
            ],
            'produk' => [
                'nama_produk' => $order->produk->nama_produk,
            ],
            'jenis_pengiriman' => $order->jenis_pengiriman,
            'total_harga' => $order->total_harga,
            'status_list' => $statusList,
        ];

        return $this->generateResponse('success', 'Detail pesanan ditemukan', $response, 200);
    }
}
