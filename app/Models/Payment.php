<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            $gross_amount = $order->total_harga;
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
                        'name' => $layanan->nama_layanan,
                    ]
                ],
                'customer_details' => [
                    'first_name' => $customer->nama_perusahaan,
                    'last_name' => '-',
                    'email' => $customer->email_perusahaan,
                    'phone' => $customer->no_telp_perusahaan,
                ],
                'callbacks' => [
                    'finish' => route('payment.finish', [
                        'order_id' => $orderId,
                        'token' => JWTAuth::fromUser($user)
                    ]),
                    'notification' => route('payment.notification'),
                ]
            ];

            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false
            ];
            Config::$isSanitized = true;
            Config::$is3ds = true;
            $snapToken = Http::withOptions(['verify' => false])->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
            ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $data_order)->json()['token'];

            Order::where('id', $orderId)
                ->update([
                    'snap_token' => $snapToken,
                    'payment_status' => 1,
                    'is_clicked' => 1,
                    'payment_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/' . $snapToken . '#/payment-list'
                ]);
            $order_data = Order::find($orderId);
            return $order_data->payment_url;
        } catch (Exception $e) {
            Log::error('Error occurred while retrieving Snap token: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'server_key' => $serverKey,
                'client_key' => $clientKey,
            ]);
            return null;
        }
    }

    public static function paymentBillingMidtrans($billing_id, $user)
    {
        try {
            $server_key = env('MIDTRANS_SERVER_KEY');
            $client_key = env('MIDTRANS_CLIENT_KEY');
            Config::$serverKey = $server_key;
            Config::$clientKey = $client_key;
            Config::$isProduction = false;
            $billing = BillingRevenue::find($billing_id);
            $gross_amount = $billing->total_akhir;
            $order_id = $billing->order_id;
            $order = Order::find($order_id);
            $m_layanan_id = $order->layanan_id;
            $layanan = Service::find($m_layanan_id);
            $m_customer_id = $user->id;
            $customer = Customer::find($m_customer_id);
            $data_order = [
                'transaction_details' => [
                    'order_id' => 'BILLING'.$billing_id,
                    'gross_amount' => $gross_amount,
                ],
                'item_details' => [
                    [
                        'id' => $m_layanan_id,
                        'price' => $gross_amount,
                        'quantity' => 1,
                        'name' => $layanan->nama_layanan,
                    ]
                ],
                'customer_details' => [
                    'first_name' => $customer->nama_perusahaan,
                    'last_name' => '-',
                    'email' => $customer->email_perusahaan,
                    'phone' => $customer->no_telp_perusahaan,
                ],
                'callbacks' => [
                    'finish' => route('billing.finish', [
                        'billing_id' => $billing_id,
                        'token' => JWTAuth::fromUser($user)
                    ]),
                    'notification' => route('payment.notification'),
                ]
            ];

            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYPEER => false
            ];
            Config::$isSanitized = true;
            Config::$is3ds = true;
            $snapToken = Http::withOptions(['verify' => false])->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($server_key . ':'),
            ])->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $data_order)->json()['token'];

            $payment_url = 'https://app.sandbox.midtrans.com/snap/v4/redirection/' . $snapToken . '#/payment-list';

            BillingRevenue::where('id', $billing_id)->update([
                'is_clicked' => 1,
                'payment_url' => $payment_url
            ]);

            return $payment_url;
        } catch (Exception $e) {
            Log::error('Error occurred while retrieving Snap token: ' . $e->getMessage(), [
                'billing_id' => $billing_id,
                'user_id' => $user,
            ]);
            return null;
        }
    }
}
