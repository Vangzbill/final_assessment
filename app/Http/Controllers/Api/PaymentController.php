<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Kontrak;
use App\Models\KontrakLayanan;
use App\Models\KontrakNodelink;
use App\Models\Nodelink;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\RiwayatDeposit;
use App\Models\Workorder;
use App\Models\WorkorderNodelink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    private function generateResponse($statusMessage, $message, $data = null, $statusCode = 200)
    {
        return response()->json([
            'status' => $statusMessage,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function createPayment(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            $orderId = $request->order_id;
            $paymentMidtrans = Payment::paymentGatewayMidtrans($orderId, $user);
            if ($paymentMidtrans) {
                return $this->generateResponse('success', 'Payment URL generated', $paymentMidtrans, 200);
            } else {
                return $this->generateResponse('error', 'Failed to generate payment', null, 500);
            }
        }
        return $this->generateResponse('error', 'Unauthorized', null, 401);
    }

    public function handleNotification(Request $request)
    {
        try {
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            if ($hashed == $request->signature_key) {
                $order = Order::find($request->order_id);
                if (!$order) {
                    return $this->generateResponse('error', 'Order not found', null, 404);
                }

                switch ($request->transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $order->payment_status = 2;
                        break;
                    case 'pending':
                        $order->payment_status = 1;
                        break;
                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $order->payment_status = 3;
                        break;
                }

                $order->save();
                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Server error'], 500);
        }
    }

    public function finishPayment(Request $request)
    {
        $token = $request->query('token');

        if (!$token || !JWTAuth::setToken($token)->check()) {
            return $this->generateResponse('error', 'Unauthorized', null, 401);
        }

        $user = JWTAuth::authenticate($token);

        $order = Order::where('id', $request->order_id)
                     ->where('customer_id', $user->id)
                     ->with('produk')
                     ->first();

        if (!$order) {
            return $this->generateResponse('error', 'Order not found', null, 404);
        }

        $order_history = new OrderStatusHistory();
        $order_history->order_id = $order->id;
        $order_history->status_id = 2;
        $order_history->keterangan = 'Pembayaran berhasil';
        $order_history->tanggal = now();
        $order_history->save();

        $order->payment_status = 2;
        $order->tanggal_pembayaran = now();
        $order->riwayat_status_order_id = $order_history->id;
        $order->save();

        $deposit = new RiwayatDeposit();
        $deposit->order_id = $order->id;
        $deposit->tipe = 'Aktif';
        $deposit->jumlah = $order->total_harga;
        $deposit->tgl_deposit = now();
        $deposit->save();

        $kontrak = new Kontrak();
        $kontrak->order_id = $order->id;
        $kontrak->customer_id = $user->id;
        $kontrak->cp_customer_id = $order->cp_customer_id;
        $kontrak->nomor_kontrak = 'KONTRAK-' . $order->unique_order;
        $kontrak->project_name = 'Penyediaan Layanan ' . $order->produk->nama_produk;
        $kontrak->start_kontrak = now();
        $kontrak->end_kontrak = now()->addYear();
        $kontrak->save();

        $workorder = new Workorder();
        $workorder->kontrak_id = $kontrak->id;
        $workorder->nomor = 'MI.' . $order->id . ' / ' . $order->unique_order . ' / XYZ' . now()->format('Y');
        $workorder->created_date = now();
        $workorder->save();

        $kontrak_layanan = new KontrakLayanan();
        $kontrak_layanan->kontrak_id = $kontrak->id;
        $kontrak_layanan->layanan_id = $order->layanan_id;
        $kontrak_layanan->produk_id = $order->produk_id;
        $kontrak_layanan->jumlah_node = $order->quantity;
        $kontrak_layanan->save();

        $kontrak_nodelink = new KontrakNodelink();
        $kontrak_nodelink->kontrak_layanan_id = $kontrak_layanan->id;
        $kontrak_nodelink->nama_perusahaan = $user->nama_perusahaan;
        $kontrak_nodelink->latitude = $user->latitude;
        $kontrak_nodelink->longitude = $user->longitude;
        $kontrak_nodelink->total_biaya = $order->total_harga;
        $kontrak_nodelink->created_date = now();
        $kontrak_nodelink->save();

        $workorder_nodelink = new WorkorderNodelink();
        $workorder_nodelink->workorder_id = $workorder->id;
        $workorder_nodelink->kontrak_nodelink_id = $kontrak_nodelink->id;
        $workorder_nodelink->created_date = now();
        $workorder_nodelink->save();

        $nodelink = new Nodelink();
        $nodelink->kontrak_nodelink_id = $kontrak_nodelink->id;
        $nodelink->sid = 'SID-' . $order->unique_order;
        $nodelink->service_line = 'SN' . $order->unique_order;
        $nodelink->created_date = now();
        $nodelink->workorder_nodelink_id = $workorder_nodelink->id;
        $nodelink->status_nodelink = 0;
        $nodelink->save();

        $kontrak_nodelink->nodelink_id = $nodelink->id;
        $kontrak_nodelink->save();

        $invoice = new Invoice();
        $invoice->kontrak_id = $kontrak->id;
        $invoice->tanggal_invoice = now();
        $invoice->tanggal_jatuh_tempo = now()->addDays(10);
        $invoice->save();

        return $this->generateResponse('success', 'Payment completed', [
            'order_id' => $order->id,
            'status' => $order->payment_status,
            'payment_url' => $order->payment_url,
        ], 200);
    }

}
