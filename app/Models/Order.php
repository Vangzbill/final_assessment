<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    protected $table = 'tbl_order';

    protected $fillable = [
        'id',
        'customer_id',
        'layanan_id',
        'alamat_customer_id',
        'cp_customer_id',
        'quantity',
        'order_date',
        'total_harga',
        'tanggal_pembayaran',
        'riwayat_status_order_id',
        'unique_order',
        'snap_token',
        'payment_status',
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function layanan()
    {
        return $this->belongsTo(Service::class, 'layanan_id', 'id');
    }

    public function cp_customer()
    {
        return $this->belongsTo(CpCustomer::class, 'cp_customer_id', 'id');
    }

    public static function createOrder($userId, $request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'customer_id' => $userId,
                'layanan_id' => $request['product'][0]['layanan_id'],
                'alamat_customer_id' => $request['alamat_customer_id'],
                'cp_customer_id' => $request['cp_customer_id'],
                'quantity' => 1,
                'total_harga' => 0,
                'order_date' => Carbon::now(),
                'unique_order' => 'ORD' . $userId . '-' . Carbon::now()->format('YmdHis'),
            ]);

            OrderStatusHistory::create([
                'user_id' => $userId,
                'order_id' => $order->id,
                'status_id' => 1,
                'keterangan' => 'Order created',
                'tanggal' => Carbon::now(),
            ]);

            for ($i = 0; $i < 2; $i++) {
                $lastInvoice = ProformaInvoice::where('order_id', $order->id)->orderBy('id', 'desc')->first();
                $proforma_invoice = ProformaInvoice::create([
                    'order_id' => $order->id,
                    'no_proforma_invoice' => 'INV' . $order->id . '-' . ($lastInvoice ? $lastInvoice->id + 1 : 1),
                    'tanggal_proforma' => Carbon::now(),
                    'tanggal_jatuh_tempo' => Carbon::now()->addDays(10),
                ]);

                if ($i == 0) {
                    $proforma_invoice_id_perangkat = $proforma_invoice->id;
                } else {
                    $proforma_invoice_id_layanan = $proforma_invoice->id;
                }
            }

            foreach ($request['product'] as $product) {
                ProformaInvoiceItem::create([
                    'order_id' => $order->id,
                    'proforma_invoice_id' => $proforma_invoice_id_perangkat,
                    'produk_id' => $product['produk_id'],
                    'quantity' => $product['quantity'],
                ]);

                ProformaInvoiceItem::create([
                    'order_id' => $order->id,
                    'proforma_invoice_id' => $proforma_invoice_id_layanan,
                    'layanan_id' => $product['layanan_id'],
                    'quantity' => $product['quantity'],
                ]);
            }

            if (!$order) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

}
