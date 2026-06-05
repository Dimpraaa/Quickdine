<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    /**
     * Generate Snap Token for an Order
     */
    public function getSnapToken($order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->transaction_id,
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => auth()->check() ? auth()->user()->name : 'Guest',
                'email' => auth()->check() ? auth()->user()->email : 'guest@quickdine.com',
            ]
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Get transaction status from Midtrans
     */
    public function getTransactionStatus(string $transactionId)
    {
        return Transaction::status($transactionId);
    }

    /**
     * Format the payment method name from Midtrans status
     */
    public function getPaymentMethodName($status, $fallbackName = 'QRIS')
    {
        $paymentName = $fallbackName;
        if (isset($status->payment_type)) {
            if ($status->payment_type == 'bank_transfer' && isset($status->va_numbers[0])) {
                $paymentName = strtoupper($status->va_numbers[0]->bank) . ' VA';
            } elseif ($status->payment_type == 'cstore' && isset($status->store)) {
                $paymentName = strtoupper($status->store);
            } elseif ($status->payment_type == 'qris') {
                $paymentName = isset($status->issuer) ? strtoupper($status->issuer) : 'QRIS';
            } elseif ($status->payment_type == 'echannel') {
                $paymentName = 'MANDIRI VA';
            } elseif ($status->payment_type == 'gopay' || $status->payment_type == 'shopeepay') {
                $paymentName = strtoupper($status->payment_type);
            } else {
                $paymentName = strtoupper(str_replace('_', ' ', $status->payment_type));
            }
        }
        return $paymentName;
    }
}
