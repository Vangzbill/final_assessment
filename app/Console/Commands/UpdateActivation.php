<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Console\Command;

class UpdateActivation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-activation';

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
        $order_delivery = Order::where('payment_status', 2)
        ->whereHas('order_status_history', function ($query) {
            $query->where('status_id', 9);
        })
        ->whereDoesntHave('order_status_history', function ($query) {
            $query->where('status_id', 5);
        })->get();

        foreach ($order_delivery as $order) {
            $lastStatus = $order->order_status_history()
            ->where('status_id', 9)
            ->orderBy('tanggal', 'desc')
            ->first();

            if ($lastStatus && $lastStatus->tanggal->diffInHours(now()) > 24) {
            $orderStatusHistory = new OrderStatusHistory();
            $orderStatusHistory->order_id = $order->id;
            $orderStatusHistory->status_id = 5;
            $orderStatusHistory->keterangan = 'Pesanan telah diaktifkan';
            $orderStatusHistory->tanggal = now();
            $orderStatusHistory->save();

            $order->riwayat_status_order_id = $orderStatusHistory->id;
            $order->save();
            }
        }
    }
}
