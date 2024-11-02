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

    public static function createOrder($request)
    {
        DB::beginTransaction();
        try {
            $order = Order::create($request->all());
            OrderStatusHistory::create([
                'user_id' => $request->customer_id,
                'order_id' => $order->id,
                'status_id' => 1,
                'keterangan' => 'Order created',
                'tanggal' => Carbon::now(),
            ]);

            if(!$order) {
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
