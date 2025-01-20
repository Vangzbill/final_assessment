<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatDeposit extends Model
{
    protected $table = 'tbl_riwayat_deposit';

    protected $fillable = [
        'id',
        'order_id',
        'tipe',
        'jumlah',
        'tgl_deposit',
    ];

    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function getDepositAktif($id)
    {
        $deposit = self::where('tipe', 'Aktif')
            ->whereHas('order', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })->with('order', 'order.kontrak.kontrak_layanan.kontrak_nodelink.nodelink')
            ->get();

        $data = [];

        foreach ($deposit as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'order_id' => $value->order_id,
                'tipe' => $value->tipe,
                'jumlah' => $value->jumlah,
                'tgl_deposit' => $value->tgl_deposit,
                'sid' => $value->order->sid ?? '',
            ];
        }
        return $data;
    }

    public static function getDepositUsed($id)
    {
        $deposit = self::where('tipe', 'Terpakai')
            ->whereHas('order', function ($query) use ($id) {
                $query->where('customer_id', $id);
            })
            ->get();

        $data = [];

        foreach ($deposit as $key => $value) {
            $data[$key] = [
                'id' => $value->id,
                'order_id' => $value->order_id,
                'tipe' => $value->tipe,
                'jumlah' => $value->jumlah,
                'tgl_deposit' => $value->tgl_deposit,
                'sid' => $value->order->sid ?? '',
            ];
        }

        return $data;
    }
}
