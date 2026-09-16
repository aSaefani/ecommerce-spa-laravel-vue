<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Order;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createPayment(Order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int)$order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'enabled_payments' => ['gopay'],
            'callbacks' => [
                'finish' => route('orders.show', $order),
                'error' => route('orders.show', $order),
                'unfinish' => route('orders.show', $order),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        return $snapToken;
    }

    public function verifyPayment($orderId)
    {
        $status = Transaction::status($orderId);
        return $status;
    }
}
