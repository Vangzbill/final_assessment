<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Console\Command;

class UpdateFinished extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-finished';

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
    //     $order_delivery = Order::where('payment_status', 2)
    //     ->whereHas('order_status_history', function ($query) {
    //         $query->where('status_id', 6);
    //     })
    //     ->whereDoesntHave('order_status_history', function ($query) {
    //         $query->where('status_id', 7);
    //     })->get();

    //     foreach ($order_delivery as $order) {
    //         $orderStatusHistory = new OrderStatusHistory();
    //         $orderStatusHistory->order_id = $order->id;
    //         $orderStatusHistory->status_id = 7;
    //         $orderStatusHistory->keterangan = 'Pesanan telah selesai';
    //         $orderStatusHistory->tanggal = now();
    //         $orderStatusHistory->save();

    //         $order->riwayat_status_order_id = $orderStatusHistory->id;
    //         $order->save();
    //     }
    }
}
