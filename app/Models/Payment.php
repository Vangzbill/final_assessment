<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;

class Payment extends Model
{
    public static function paymentGatewayMidtrans($orderId, $user)
    {
        try {
            $serverKey = env('MIDTRANS_SERVER_KEY');
            $clientKey = env('MIDTRANS_CLIENT_KEY');
            Config::$serverKey = $serverKey;
            Config::$clientKey = $clientKey;
            Config::$isProduction = false;
            $order = Order::find($orderId);
            $gross_amount = $order->amount;
            $m_layanan_id = $order->layanan_id;
            $layanan = Service::find($m_layanan_id);
            $m_customer_id = $user->id;
            $customer = Customer::find($m_customer_id);
            $data_order = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $gross_amount,
                ],
                'item_details' => [
                    [
                        'id' => $m_layanan_id,
                        'price' => $gross_amount,
                        'quantity' => 1,
                        'name' => $layanan->nama,
                    ]
                ],
                'customer_details' => [
                    'first_name' => $customer->nama_perusahaan,
                    'last_name' => '',
                    'email' => $customer->email_perusahaan,
                    'phone' => $customer->no_telp_perusahaan,
                ]
            ];

            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false
            ];
            Config::$isProduction = false;
            Config::$isSanitized = true;
            Config::$is3ds = true;
            $snapToken = Snap::getSnapToken($data_order);
            Order::table('h_order')
                ->where('id', $orderId)
                ->update([
                    'snap_token' => $snapToken,
                    'payment_status' => 1,
                ]);
            return $snapToken;
        } catch (Exception $e) {
            Log::error('Error occurred while retrieving Snap token: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'server_key' => $serverKey,  // To check if it's null
                'client_key' => $clientKey,  // To check if it's null
            ]);
            return null;
        }
    }
}
