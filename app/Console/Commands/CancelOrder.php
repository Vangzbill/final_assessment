<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = Order::where('payment_status', null)
            ->orWhere('payment_status', 1)
            ->whereRaw('DATEDIFF(NOW(), order_date) > 10')
            ->get();

        Log::info('Order yang dibatalkan: ' . $orders->count());

        foreach ($orders as $order) {
            $existingHistory = OrderStatusHistory::where('order_id', $order->id)
            ->where('status_id', 8)
            ->first();

            if (!$existingHistory) {
                $orderHistory = new OrderStatusHistory();
                $orderHistory->order_id = $order->id;
                $orderHistory->status_id = 8;
                $orderHistory->keterangan = 'Pesanan dibatalkan oleh sistem';
                $orderHistory->tanggal = now();
                $orderHistory->save();

                $order->riwayat_status_order_id = $orderHistory->id;
                $order->save();
            }
        }
    }
}
