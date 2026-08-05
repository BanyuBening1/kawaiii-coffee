<?php

namespace App\Services;

use App\Models\Transactions;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    // =========================
    // Buat Snap Token
    // =========================
    public function createSnapToken(Transactions $transaction, array $itemDetails, $user): string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->transaction_code,
                'gross_amount' => (int) $transaction->total,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
        ];

        return Snap::getSnapToken($params);
    }

    // =========================
    // Buat QRIS Core API
    // =========================
    public function createQris(Transactions $transaction, array $itemDetails): ?string
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id'     => $transaction->transaction_code,
                'gross_amount' => (int) $transaction->total,
            ],
            'item_details' => $itemDetails,
        ];

        $response = CoreApi::charge($params);

        foreach ($response->actions as $action) {
            if ($action->name === 'generate-qr-code') {
                return $action->url;
            }
        }

        return null;
    }

    // =========================
    // Handle Callback Midtrans
    // =========================
    public function handleCallback(array $payload): string
    {
        $transactionStatus = $payload['transaction_status'] ?? null;
        $orderId           = $payload['order_id'] ?? null;
        $fraudStatus       = $payload['fraud_status'] ?? null;
        $paymentType       = $payload['payment_type'] ?? null;

        // Verifikasi signature
        $serverKey = config('midtrans.server_key');

        $signatureKey = hash(
            'sha512',
            $orderId .
            $payload['status_code'] .
            $payload['gross_amount'] .
            $serverKey
        );

        if ($signatureKey !== ($payload['signature_key'] ?? null)) {
            Log::warning('Midtrans invalid signature', [
                'order_id' => $orderId,
            ]);

            throw new \Exception('Invalid signature', 403);
        }

        $transaction = Transactions::where('transaction_code', $orderId)->firstOrFail();

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($fraudStatus === 'accept' || $fraudStatus === null) {
                if ($transaction->status === 'pending') {
                    $transaction->update([
                        'status'         => 'paid',
                        'payment_method' => $paymentType ?? 'midtrans',
                        'paid_amount'    => $transaction->total,
                        'change_amount'  => 0,
                    ]);

                    return 'paid';
                }
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $transaction->update([
                'status' => 'cancelled',
            ]);

            return 'cancelled';
        }

        return 'ignored';
    }
}