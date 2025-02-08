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
        'payment_url',
        'sn_kit',
        'is_ttd'
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

    public function kontrak()
    {
        return $this->hasMany(Kontrak::class, 'order_id', 'id');
    }

    public function riwayat_deposit()
    {
        return $this->hasOne(RiwayatDeposit::class, 'order_id', 'id');
    }

    public function billing_revenue()
    {
        return $this->hasMany(BillingRevenue::class, 'order_id', 'id');
    }

    public function proforma_invoice()
    {
        return $this->hasMany(ProformaInvoice::class, 'order_id', 'id');
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
                'sn_kit' => 'KITSN' . $userId . '-' . (Order::max('id') + 1) . '-' . Carbon::now()->format('YmdHis'),
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
                'deposit_layanan' => $harga_layanan,
                'biaya_pengiriman' => 0,
                'ppn' => $harga_perangkat * 0.11,
                'total_keseluruhan' => $harga_perangkat + ($harga_perangkat * 0.11) + $harga_layanan + 16000,
            ]);

            // $proforma_invoice_layanan = ProformaInvoice::create([
            //     'order_id' => $order->id,
            //     'no_proforma_invoice' => 'INV' . $order->id . '-' . ($lastInvoice ? $lastInvoice->id + 1 : 1),
            //     'tanggal_proforma' => Carbon::now(),
            //     'tanggal_jatuh_tempo' => Carbon::now()->addDays(10),
            //     'biaya_perangkat' => 0,
            //     'deposit_layanan' => $harga_layanan,
            //     'biaya_pengiriman' => 0,
            //     'ppn' => 0,
            //     'total_keseluruhan' => $harga_layanan,
            // ]);

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
                'proforma_invoice_id' => $proforma_invoice_perangkat->id,
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

    public static function getListOrder($userId, $statusIds = null, $page = 1)
    {
        $query = Order::with([
            'layanan',
            'produk',
            'cp_customer',
            'order_status_history',
            'order_status_history.status',
            'proforma_invoice_item',
            'proforma_invoice_item.produk',
            'produk.category',
            'proforma_invoice_item.layanan'
        ])
            ->where('customer_id', $userId);

        if ($statusIds) {
            $query->whereHas('order_status_history', function ($query) use ($statusIds) {
                $query->whereIn('status_id', $statusIds)
                    ->where(function ($subQuery) {
                        $subQuery->whereRaw('id = (SELECT MAX(id) FROM tbl_riwayat_status_order AS osh WHERE osh.order_id = tbl_order.id)');
                    });
            });
        }

        $orders = $query->orderBy('tbl_order.id', 'desc')->paginate(10, ['*'], 'page', $page);

        $orders->getCollection()->transform(function ($item) {
            $lastStatusHistory = optional($item->order_status_history->last());
            $statusOrder = optional($lastStatusHistory->status)->nama_status_order;
            $statusId = optional($lastStatusHistory)->status_id;
            if ($item->proforma_invoice_item->isEmpty()) {
                $imageFileName = null;
                $kategori = null;
            } else {
                $firstProforma = $item->proforma_invoice_item->first();
                if ($firstProforma && $firstProforma->produk) {
                    $imageFileName = $firstProforma->produk->gambar_order;
                    $kategori = optional($firstProforma->produk->category)->nama_kategori;
                } else {
                    $imageFileName = null;
                    $kategori = null;
                }
            }
            $image = $imageFileName ? asset('assets/images/' . $imageFileName) : null;

            $kategori = optional(optional($item->produk)->category)->nama_kategori;

            $tanggalOrder = Carbon::parse($item->order_date)->locale('id')->translatedFormat('d F Y');

            $totalHarga = $item->total_harga < 16000 ? 16000 + $item->total_harga : $item->total_harga;

            return [
                'id' => $item->id,
                'order_id' => $item->unique_order,
                'status' => $statusOrder,
                'status_id' => $statusId,
                'image' => $image,
                'kategori' => $kategori,
                'tanggal_order' => $tanggalOrder,
                'total_keseluruhan' => $totalHarga,
            ];
        });

        return $orders;
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

    public static function getActivation($orderId, $userId)
    {
        $order = Order::with([
            'layanan',
            'produk',
            'customer',
            'cp_customer',
            'kontrak',
            'proforma_invoice_item',
            'proforma_invoice_item.produk',
            'proforma_invoice_item.layanan',
            'kontrak.kontrak_layanan.kontrak_nodelink.nodelink'
        ])->where('id', $orderId)
            ->where('customer_id', $userId)
            ->first();

        $formatTanggal = function ($tanggal) {
            return Carbon::parse($tanggal)->translatedFormat('d F Y');
        };

        $initialNama = substr(optional($order->cp_customer)->nama, 0, 1);

        $data = [
            'unique_order' => $order->unique_order,
            'nama_perusahaan' => optional($order->customer)->nama_perusahaan,
            'nama_perangkat' => optional($order->proforma_invoice_item->first()->produk)->nama_produk,
            'nama_layanan' => optional($order->proforma_invoice_item()->whereNotNull('layanan_id')->first()->layanan)->nama_layanan,
            'order_date' => $formatTanggal($order->order_date),
            'nama' => optional($order->cp_customer)->nama,
            'email' => optional($order->cp_customer)->email,
            'no_telp' => optional($order->cp_customer)->no_telp,
            'harga_perangkat' => optional($order->proforma_invoice_item->first()->produk)->harga_produk,
            'ppn' => $order->proforma_invoice_item->sum('nilai_ppn'),
            'deposit_layanan' => optional($order->layanan)->harga_layanan,
            'total_biaya' => $order->total_harga,
            'tgl_aktivasi' => Carbon::now()->translatedFormat('d F Y'),
            'nomor_kontrak' => $order->kontrak->first()->nomor_kontrak,
            'kit_serial_number' => $order->sn_kit ? $order->sn_kit : '-',
            'sid' => $order->sid,
            'is_ttd' => $order->is_ttd,
            'initial_nama' => $initialNama,
        ];

        return $data;
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
            'proforma_invoice_item.layanan',
            'kontrak.kontrak_layanan.kontrak_nodelink.nodelink'
        ])->where('id', $orderId)
            ->where('customer_id', $userId)
            ->first();

        $formatTanggal = function ($tanggal) {
            return $tanggal ? Carbon::parse($tanggal)->translatedFormat('d F Y') : null;
        };

        $isCanceled = $order->order_status_history
            ->contains(function ($item) {
                return $item->status->nama_status_order === 'Pesanan Dibatalkan';
            });

        $requiredStatuses = $isCanceled
            ? ['Pesanan Dibatalkan', 'Pesanan Selesai']
            : [
                'Pembayaran',
                'Pengiriman',
                'Pesanan Diterima',
                'Aktivasi Layanan',
                'Surat Pernyataan Aktivasi',
                'Pesanan Selesai'
            ];

        $existingStatuses = $order->order_status_history
            ->filter(function ($item) {
                return $item->status->nama_status_order !== 'Order Diterima ';
            })
            ->mapWithKeys(function ($item) {
                return [$item->status->nama_status_order => $item];
            });

        $riwayatStatus = collect($requiredStatuses)
            ->map(function ($statusName) use ($existingStatuses, $formatTanggal, $order) {
                $existingStatus = $existingStatuses->get($statusName);
                $baseStatus = [
                    'status' => $statusName,
                    'keterangan' => $existingStatus ? $existingStatus->keterangan : '',
                    'tanggal' => $existingStatus ? $formatTanggal($existingStatus->tanggal) : null,
                    'is_done' => $existingStatus ? 1 : 0,
                ];

                switch ($statusName) {
                    case 'Pembayaran':
                        $baseStatus['harga'] = $order->total_harga;
                        $baseStatus['estimasi_pengambilan'] = $existingStatus ? $formatTanggal($existingStatus->tanggal) : null;
                        break;

                    case 'Pengiriman':
                        if ($existingStatus) {
                            $baseStatus['estimasi'] = $formatTanggal(
                                Carbon::now()
                            );
                        }
                        $baseStatus['estimasi_pengambilan'] = Carbon::now()->translatedFormat('d F Y');
                        break;

                    case 'Aktivasi Layanan':
                        if ($existingStatus) {
                            $nodelink = null;
                            if ($order->kontrak) {
                                $kontrak = $order->kontrak->first();
                                if ($kontrak && $kontrak->kontrak_layanan->isNotEmpty()) {
                                    $kontrakLayanan = $kontrak->kontrak_layanan->first();
                                    if ($kontrakLayanan && $kontrakLayanan->kontrak_nodelink->isNotEmpty()) {
                                        $kontrakNodelink = $kontrakLayanan->kontrak_nodelink->first();
                                        if ($kontrakNodelink) {
                                            $nodelink = $kontrakNodelink->nodelink;
                                        }
                                    }
                                }
                            }

                            $baseStatus['sn_kit'] = $order->sn_kit;
                            $baseStatus['latitude'] = optional($nodelink)->latitude;
                            $baseStatus['longitude'] = optional($nodelink)->longitude;
                        }
                        break;

                    case 'Surat Pernyataan Aktivasi':
                        $baseStatus['is_ttd'] = $order->is_ttd;
                        break;
                }

                return $baseStatus;
            })
            ->values();

        $imageFileName = optional($order->proforma_invoice_item->first()->produk)->gambar_order;
        $image = $imageFileName ? asset('assets/images/' . $imageFileName) : null;

        $data = [
            'order_id' => $order->id,
            'unique_order' => $order->unique_order,
            'nama_perangkat' => optional($order->proforma_invoice_item->first()->produk)->nama_produk,
            'nama_kategori' => optional(optional($order->produk)->category)->nama_kategori,
            'image' => $image,
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

        if ((float) $order->total_harga < 16000) {
            $total_keseluruhan = $order->total_harga + 16000;
        } else {
            $total_keseluruhan = $order->total_harga;
        }

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
                'deposit_layanan' => optional($order->layanan)->harga_layanan,
                'biaya_asuransi' => 16000,
                'harga_perangkat' => optional($order->proforma_invoice_item->first()->produk)->harga_produk,
                'total_biaya' => optional($order->proforma_invoice_item->first()->produk)->harga_produk + 16000,
                'ppn' => $order->proforma_invoice_item->sum('nilai_ppn'),
                'total_keseluruhan' => $total_keseluruhan,
            ]
        ];

        return $data;
    }

    public static function cekOrder($id, $userId)
    {
        $order = Order::where('id', $id)
            ->where('customer_id', $userId)
            ->with('order_status_history', 'order_status_history.status')
            ->first();

        return $order->order_status_history->last()->status_id;
    }

    public static function deliveredOrder($orderId, $userId)
    {
        try {
            DB::beginTransaction();
            $order = Order::where('id', $orderId)
                ->where('customer_id', $userId)
                ->first();

            $orderStatusHistory = new OrderStatusHistory();
            $orderStatusHistory->order_id = $order->id;
            $orderStatusHistory->status_id = 4;
            $orderStatusHistory->keterangan = 'Pesanan telah diterima';
            $orderStatusHistory->tanggal = now();
            $orderStatusHistory->save();

            $order->riwayat_status_order_id = $orderStatusHistory->id;
            $order->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return 'Pesanan telah diterima';
    }
}
