<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\OrderStatusHistory;

class Nodelink extends Model
{
    protected $table = 'tbl_nodelink';

    protected $fillable = [
        'id',
        'kontrak_nodelink_id',
        'sid',
        'service_line',
        'created_date',
        'workorder_nodelink_id',
        'status_nodelink',
        'latitude',
        'longitude',
    ];

    public $timestamps = false;

    public function kontrak_nodelink()
    {
        return $this->belongsTo(KontrakNodelink::class, 'kontrak_nodelink_id', 'id');
    }

    public static function activateOrder($order_id, $user_id, $latitude, $longitude)
    {
        $nodelink = Nodelink::where('status_nodelink', '0')
            ->whereNull('latitude')
            ->whereNull('longitude')
            ->whereHas('kontrak_nodelink.kontrak_layanan.kontrak', function($query) use ($order_id) {
                $query->where('order_id', $order_id);
            })
            ->first();

        if(!$nodelink) {
            return false;
        }

        try {
            DB::beginTransaction();

            $nodelink->latitude = $latitude;
            $nodelink->longitude = $longitude;
            $nodelink->status_nodelink = '1';
            $nodelink->save();

            $riwayatStatusOrder = new OrderStatusHistory();
            $riwayatStatusOrder->order_id = $order_id;
            $riwayatStatusOrder->status_id = 5;
            $riwayatStatusOrder->keterangan = 'Pesanan telah diaktifkan';
            $riwayatStatusOrder->tanggal = now();
            $riwayatStatusOrder->save();

            $order = Order::find($order_id);
            $order->riwayat_status_order_id = $riwayatStatusOrder->id;
            $order->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
