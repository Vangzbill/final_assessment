<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BillingRevenue extends Model
{
    protected $table = 'tbl_billing_revenue';

    protected $fillable = [
        'id',
        'kontrak_nodelink_id',
        'order_id',
        'tanggal_tagih',
        'total_ppn',
        'total_tagihan',
        'total_akhir',
        'jatuh_tempo',
        'status',
        'bukti_ppn',
        'is_clicked',
        'payment_url'
    ];

    public $timestamps = false;

    public function kontrak_nodelink()
    {
        return $this->belongsTo(KontrakNodelink::class, 'kontrak_nodelink_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function getBilling($id, $user_id)
    {
        $billing_data = BillingRevenue::with([
            'order',
            'kontrak_nodelink.kontrak_layanan.kontrak.order.layanan',
            'kontrak_nodelink.kontrak_layanan.kontrak.order.customer',
            'kontrak_nodelink.kontrak_layanan.kontrak.order.proforma_invoice',
        ])
            ->where('id', $id)
            ->whereHas('kontrak_nodelink.kontrak_layanan.kontrak.order', function ($query) use ($user_id) {
                $query->where('customer_id', $user_id);
            })
            ->first();
        if ($billing_data) {
            $proformaInvoice = $billing_data->order->proforma_invoice->first();
            $customer = $billing_data->order->customer->first();
            $no_proforma_invoice = $proformaInvoice ? $proformaInvoice->no_proforma_invoice : 'N/A';

            $nama_layanan = $billing_data->kontrak_nodelink->kontrak_layanan->kontrak->order->layanan->nama_layanan ?? 'N/A';
            $nama_customer = $customer ? $customer->nama_perusahaan : 'N/A';
            $alamat_customer = $customer ? $customer->alamat : 'N/A';
            $bulan = Carbon::parse($billing_data->tanggal_tagih)->format('mm');
            $tahun = Carbon::parse($billing_data->tanggal_tagih)->format('Y');
            $periode = Carbon::parse($billing_data->tanggal_tagih)->format('F') . ' ' . $tahun;

            return [
                'nama_layanan' => $nama_layanan,
                'nama_customer' => $nama_customer,
                'alamat_customer' => $alamat_customer,
                'no_proforma_invoice' => $no_proforma_invoice,
                'total_tagihan' => $billing_data->total_tagihan,
                'total_akhir' => $billing_data->total_akhir,
                'tanggal_tagih' => $billing_data->tanggal_tagih,
                'jatuh_tempo' => $billing_data->jatuh_tempo,
                'status' => $billing_data->status,
                'total_ppn' => $billing_data->total_ppn,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'periode' => $periode,
            ];
        }

        return null;
    }
}
