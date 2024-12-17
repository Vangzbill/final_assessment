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
        'produk_id',
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
        'payment_url'
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

    public function produk()
    {
        return $this->belongsTo(Product::class, 'produk_id', 'id');
    }

    public function cp_customer()
    {
        return $this->belongsTo(CpCustomer::class, 'cp_customer_id', 'id');
    }

    public function order_status_history()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');
    }

    public function proforma_invoice_item()
    {
        return $this->hasMany(ProformaInvoiceItem::class, 'order_id', 'id');
    }

    public static function createOrder($userId, $request)
    {
        DB::beginTransaction();
        try {
            $cp_customer = CpCustomer::create([
                'customer_id' => $userId,
                'nama' => $request['nama_cp'],
                'email' => $request['email_cp'],
                'no_telp' => $request['no_telp_cp'],
            ]);

            $layanan = Service::where('nama_layanan', $request['nama_layanan'])->where('produk_id', $request['produk_id'])->first();

            $harga_perangkat = Product::find($request['produk_id'])->harga_produk;
            $harga_layanan = $layanan->harga_layanan;
            $layanan_id = $layanan->id;
            $order = Order::create([
                'customer_id' => $userId,
                'layanan_id' => $layanan_id,
                'produk_id' => $request['produk_id'],
                'alamat_customer_id' => 0,
                'cp_customer_id' => $cp_customer->id,
                'quantity' => 1,
                'total_harga' => ($harga_perangkat * 0.11) + $harga_layanan + $harga_perangkat + 16000,
                'order_date' => Carbon::now(),
                'unique_order' => 'ORD' . $userId . '-' . Carbon::now()->format('YmdHis'),
            ]);

            $riwayat_order = OrderStatusHistory::create([
                'user_id' => $userId,
                'order_id' => $order->id,
                'status_id' => 1,
                'keterangan' => 'Order created',
                'tanggal' => Carbon::now(),
            ]);

            $order_data = Order::find($order->id);
            $order_data->riwayat_status_order_id = $riwayat_order->id;
            $order_data->save();

            $lastInvoice = ProformaInvoice::where('order_id', $order->id)->orderBy('id', 'desc')->first();

            $proforma_invoice_perangkat = ProformaInvoice::create([
                'order_id' => $order->id,
                'no_proforma_invoice' => 'INV' . $order->id . '-' . ($lastInvoice ? $lastInvoice->id + 1 : 1),
                'tanggal_proforma' => Carbon::now(),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(10),
                'biaya_perangkat' => $harga_perangkat,
                'deposit_layanan' => 0,
                'biaya_pengiriman' => 0,
                'ppn' => $harga_perangkat * 0.11,
                'total_bayar' => $harga_perangkat + ($harga_perangkat * 0.11),
            ]);

            $proforma_invoice_layanan = ProformaInvoice::create([
                'order_id' => $order->id,
                'no_proforma_invoice' => 'INV' . $order->id . '-' . ($lastInvoice ? $lastInvoice->id + 1 : 1),
                'tanggal_proforma' => Carbon::now(),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(10),
                'biaya_perangkat' => 0,
                'deposit_layanan' => $harga_layanan,
                'biaya_pengiriman' => 0,
                'ppn' => 0,
                'total_bayar' => $harga_layanan,
            ]);

            ProformaInvoiceItem::create([
                'order_id' => $order->id,
                'proforma_invoice_id' => $proforma_invoice_perangkat->id,
                'produk_id' => $request['produk_id'],
                'quantity' => 1,
                'nilai_pokok' => $harga_perangkat,
                'nilai_ppn' => $harga_perangkat * 0.11,
                'total_bayar' => $harga_perangkat + ($harga_perangkat * 0.11),
            ]);

            ProformaInvoiceItem::create([
                'order_id' => $order->id,
                'proforma_invoice_id' => $proforma_invoice_layanan->id,
                'layanan_id' => $layanan->id,
                'quantity' => 1,
                'nilai_pokok' => $harga_layanan,
                'nilai_ppn' => 0,
                'total_bayar' => $harga_layanan,
            ]);

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

    public static function getListOrder($userId)
    {
        $order = Order::with(['layanan', 'produk', 'cp_customer', 'order_status_history', 'order_status_history.status', 'proforma_invoice_item', 'proforma_invoice_item.produk', 'proforma_invoice_item.layanan'])
            ->where('customer_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return $order;
    }

    public static function getOrder($orderId, $userId)
    {
        $order = Order::with(['layanan', 'cp_customer', 'order_status_history', 'order_status_history.status', 'proforma_invoice_item', 'proforma_invoice_item.produk', 'proforma_invoice_item.layanan'])
            ->where('id', $orderId)
            ->where('customer_id', $userId)
            ->first();

        if ((float) $order->total_harga < 16000) {
            $total_keseluruhan = $order->total_harga + 16000;
        } else {
            $total_keseluruhan = $order->total_harga;
        }

        if ($order) {
            return [
                'id' => $order->id,
                'order_id' => $order->unique_order,
                'nama_perangkat' => optional($order->proforma_invoice_item->first()->produk)->nama_produk,
                'nama_layanan' => optional($order->proforma_invoice_item()->whereNotNull('layanan_id')->first()->layanan)->nama_layanan,
                'nama_cp' => optional($order->cp_customer)->nama,
                'email_cp' => optional($order->cp_customer)->email,
                'no_telp_cp' => optional($order->cp_customer)->no_telp,
                'biaya_asuransi' => 16000,
                'harga_perangkat' => optional($order->proforma_invoice_item->first()->produk)->harga_produk,
                'total_biaya' => optional($order->proforma_invoice_item->first()->produk)->harga_produk + 16000,
                'ppn' => $order->proforma_invoice_item->sum('nilai_ppn'),
                'deposit_layanan' => optional($order->layanan)->harga_layanan,
                'total_keseluruhan' => $total_keseluruhan,
            ];
        }

        return null;
    }

    public static function getOrderDetail($orderId, $userId)
    {
        $order = Order::with([
            'layanan',
            'produk',
            'cp_customer',
            'order_status_history',
            'order_status_history.status',
            'proforma_invoice_item',
            'proforma_invoice_item.produk',
            'proforma_invoice_item.layanan'
        ])->where('id', $orderId)
            ->where('customer_id', $userId)
            ->first();

        $formatTanggal = function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        };

        $riwayatStatus = $order->order_status_history
            ->filter(function ($item) {
                return $item->status->nama_status_order !== 'Order Confirmed';
            })
            ->map(function ($item) use ($formatTanggal) {
                return [
                    'status' => $item->status->nama_status_order,
                    'keterangan' => $item->keterangan,
                    'tanggal' => $formatTanggal($item->tanggal),
                ];
            })
            ->values();

        $data = [
            'unique_order' => $order->unique_order,
            'nama_perangkat' => optional($order->proforma_invoice_item->first()->produk)->nama_produk,
            'order_date' => $formatTanggal($order->order_date),
            'riwayat_status_order' => $riwayatStatus,
        ];

        return $data;
    }

    public static function getOrderSummary($orderId, $userId)
    {
        $order = Order::with([
            'layanan',
            'produk',
            'cp_customer',
            'order_status_history',
            'order_status_history.status',
            'proforma_invoice_item',
            'proforma_invoice_item.produk',
            'proforma_invoice_item.layanan'
        ])->where('id', $orderId)
            ->where('customer_id', $userId)
            ->first();

        $formatTanggal = function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        };

        $data = [
            'unique_order' => $order->unique_order,
            'nama_perangkat' => optional($order->proforma_invoice_item->first()->produk)->nama_produk,
            'order_date' => $formatTanggal($order->order_date),
            'penerima' => [
                'nama' => optional($order->cp_customer)->nama,
                'email' => optional($order->cp_customer)->email,
                'no_telp' => optional($order->cp_customer)->no_telp,
            ],
            'rincian' => [
                'harga_perangkat' => optional($order->proforma_invoice_item->first()->produk)->harga_produk,
                'ppn' => $order->proforma_invoice_item->sum('nilai_ppn'),
                'deposit_layanan' => optional($order->layanan)->harga_layanan,
                'total_biaya' => $order->total_harga,
            ]
        ];

        return $data;
    }
}
