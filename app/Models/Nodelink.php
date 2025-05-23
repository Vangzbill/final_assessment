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
        'nama_node',
        'alamat_node',
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

        $customer = Customer::find($user_id);

        try {
            DB::beginTransaction();

            $inisial_perusahaan = implode('', array_map(function($word) {
                return strtoupper(substr($word, 0, 1));
            }, explode(' ', $customer->nama_perusahaan)));

            $nodelink->latitude = $latitude;
            $nodelink->longitude = $longitude;
            $nodelink->status_nodelink = '1';
            $nodelink->nama_node = 'Node ' . $inisial_perusahaan . '-' . $nodelink->id;
            $nodelink->save();

            $riwayatStatusOrder = new OrderStatusHistory();
            $riwayatStatusOrder->order_id = $order_id;
            $riwayatStatusOrder->status_id = 5;
            $riwayatStatusOrder->keterangan = 'Pesanan telah diaktifkan';
            $riwayatStatusOrder->tanggal = now();
            $riwayatStatusOrder->save();

            $order = Order::find($order_id);
            $order->riwayat_status_order_id = $riwayatStatusOrder->id;
            $order->nama_node = $nodelink->nama_node;
            $order->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public static function addAddressNode($order_id, $user_id, $provinsi, $kabupaten)
    {
        $nodelink = Nodelink::whereNull('alamat_node')
            ->whereHas('kontrak_nodelink.kontrak_layanan.kontrak', function($query) use ($order_id) {
                $query->where('order_id', $order_id);
            })
            ->first();

        if(!$nodelink) {
            return false;
        }

        $customer = Customer::find($user_id);

        try {
            DB::beginTransaction();

            $nodelink->alamat_node = $provinsi . ', ' . $kabupaten;
            $nodelink->save();

            $riwayatStatusOrder = new OrderStatusHistory();
            $riwayatStatusOrder->order_id = $order_id;
            $riwayatStatusOrder->status_id = 9;
            $riwayatStatusOrder->keterangan = 'Alamat node telah ditambahkan';
            $riwayatStatusOrder->tanggal = now();
            $riwayatStatusOrder->save();

            $order = Order::find($order_id);
            $order->alamat_node = $nodelink->alamat_node;
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
