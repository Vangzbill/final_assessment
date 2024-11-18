<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Console\Command;

class UpdateDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-delivery';

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
        $orders = Order::where('payment_status', 2)->get();
        foreach ($orders as $order) {
            $orderHistory = new OrderStatusHistory();
            $orderHistory->order_id = $order->id;
            $orderHistory->status_id = 3;
            $orderHistory->keterangan = 'Pesanan sedang dikirim';
            $orderHistory->tanggal = now();
            $orderHistory->save();

            $order->riwayat_status_order_id = $orderHistory->id;
            $order->save();
        }
    }
}
