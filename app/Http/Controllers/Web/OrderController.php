<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['produk', 'layanan', 'order_status_history.status', 'cp_customer', 'customer'])
                ->select('tbl_order.*');

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('no', function ($order) {
                    return '';
                })
                ->addColumn('unique_order', function ($order) {
                    return $order->unique_order;
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
                    return '<a href="" class="btn btn-primary btn-sm">View</a>
                        <a href="" class="btn btn-warning btn-sm">Update Status</a>';
                })
                ->rawColumns(['customer', 'produk', 'jenis_pengiriman', 'status', 'action'])
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
                                    $q2->where('nama', 'LIKE', "%{$search}%");
                                });
                            });
                    }
                })
                ->make(true);
        }
        return view('admin.pages.order.index');
    }

}
