<?php

namespace App\Services;

use App\Models\DetailTransaction;
use App\Models\Ingredients;
use App\Models\Notification;
use App\Models\ProductIngredients;
use App\Models\Products;
use App\Models\StockMovement;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class TransactionService
{
    // =========================
    // Validasi & Hitung Items
    // =========================
    public function calculateItems(array $items): array
    {
        $subtotal    = 0;
        $details     = [];
        $itemDetails = [];

        foreach ($items as $item) {
            $product = Products::findOrFail($item['product_id']);
            $qty     = $item['quantity'];
            $price   = (float) $product->selling_price;
            $cost    = (float) $product->cost_price;

            $itemSubtotal = $price * $qty;
            $subtotal    += $itemSubtotal;

            $recipes = ProductIngredients::where('product_id', $product->id)->get();
            foreach ($recipes as $recipe) {
                $ingredient = Ingredients::find($recipe->ingredient_id);
                $needed     = $recipe->quantity * $qty;
                if ($ingredient->stock < $needed) {
                    throw new \Exception("Stok bahan '{$ingredient->name}' tidak cukup.");
                }
            }

            $details[] = [
                'product_id' => $product->id,
                'quantity'   => $qty,
                'unit_price' => $price,
                'unit_cost'  => $cost,
                'subtotal'   => $itemSubtotal,
            ];

            $itemDetails[] = [
                'id'       => (string) $product->id,
                'price'    => (int) $price,
                'quantity' => $qty,
                'name'     => substr($product->name, 0, 50),
            ];
        }

        return compact('subtotal', 'details', 'itemDetails');
    }

    // =========================
    // Buat Transaksi
    // =========================
    public function createTransaction(array $data): Transactions
    {
        return Transactions::create([
            'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
            'transaction_date' => now(),
            'subtotal'         => $data['subtotal'],
            'total'            => $data['subtotal'],
            'paid_amount'      => $data['paid_amount'] ?? 0,
            'change_amount'    => $data['change_amount'] ?? 0,
            'payment_method'   => $data['payment_method'],
            'status'           => $data['status'],
            'cashier_id'       => $data['cashier_id'],
        ]);
    }

    // =========================
    // Simpan Detail Transaksi
    // =========================
    public function saveDetails(Transactions $transaction, array $details): void
    {
        foreach ($details as $detail) {
            $detail['transaction_id'] = $transaction->id;
            DetailTransaction::create($detail);
        }
    }

    // =========================
    // Kurangi Stok Bahan
    // =========================
    public function deductStock(Transactions $transaction, array $details): void
    {
        $cashier = User::find($transaction->cashier_id);

        foreach ($details as $detail) {
            $recipes = ProductIngredients::where('product_id', $detail['product_id'])->get();

            foreach ($recipes as $recipe) {
                $ingredient = Ingredients::find($recipe->ingredient_id);
                $used       = $recipe->quantity * $detail['quantity'];

                $ingredient->stock -= $used;
                $ingredient->save();

                $movement = new StockMovement([
                    'ingredient_id' => $ingredient->id,
                    'user_id'       => $transaction->cashier_id,
                    'type'          => 'OUT',
                    'quantity'      => $used,
                    'reference'     => $transaction->transaction_code,
                    'description'   => 'Penggunaan bahan dari transaksi',
                ]);
                $movement->skipObserver = true;
                $movement->save();

                if ($ingredient->stock < $ingredient->min_stock) {
                    // PERBAIKAN: Filter anti-spam dimatikan agar notifikasi stok selalu dikirim saat testing
                    $existsForCashier = false;

                    if ($cashier && !$existsForCashier) {
                        // Simpan notifikasi ke database untuk kasir
                        notify(
                            'Stok Menipis',
                            'Stok ' . $ingredient->name . ' hampir habis (sisa: ' . $ingredient->stock . ' ' . $ingredient->unit . ')',
                            'low_stock',
                            'ingredient_' . $ingredient->id,
                            $cashier->id
                        );

                        // Kirim FCM ke kasir
                        $this->sendFcmToUser(
                            $cashier,
                            'Stok Menipis',
                            'Stok ' . $ingredient->name . ' hampir habis (sisa: ' . $ingredient->stock . ' ' . $ingredient->unit . ')',
                            ['type' => 'low_stock', 'ingredient_id' => (string) $ingredient->id]
                        );
                    }
                }
            }
        }
    }

    // =========================
    // Kirim Notifikasi Transaksi
    // =========================
    public function sendNotifications(Transactions $transaction): void
    {
        $cashier = User::find($transaction->cashier_id);

        if ($cashier) {
            // Simpan notifikasi ke database
            notify(
                'Transaksi Berhasil',
                'Transaksi ' . $transaction->transaction_code . ' berhasil dengan total Rp ' . number_format($transaction->total, 0, ',', '.'),
                'transaction',
                'transaction_' . $transaction->id,
                $cashier->id
            );

            // Kirim FCM ke kasir
            $this->sendFcmToUser(
                $cashier,
                'Transaksi Berhasil',
                'Transaksi ' . $transaction->transaction_code . ' berhasil — Rp ' . number_format($transaction->total, 0, ',', '.'),
                ['type' => 'transaction', 'transaction_id' => (string) $transaction->id]
            );
        }
    }

    // =========================
    // Kirim FCM Push Notification
    // =========================
    private function sendFcmToUser(User $user, string $title, string $body, array $data = []): void
    {
        try {
            if (empty($user->fcm_token)) {
                Log::info('FCM skip — token kosong untuk user: ' . $user->name);
                return;
            }

            if (!app()->bound('firebase.messaging')) {
                Log::error('FCM error: Service firebase.messaging belum terdaftar di Laravel Provider.');
                return;
            }

            $messaging = app('firebase.messaging');

            // Menggunakan sintaks yang kompatibel lintas versi kreait SDK
            $message = CloudMessage::new()
                ->toToken($user->fcm_token)
                ->withNotification(FcmNotification::create($title, $body))
                ->withData(array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]));

            $messaging->send($message);

            Log::info('FCM berhasil dikirim ke ' . $user->name . ': ' . $title);

        } catch (\Throwable $e) { 
            Log::error('FCM Fatal Error: ' . $e->getMessage() . ' di file ' . $e->getFile() . ' baris ' . $e->getLine());
        }
    }
}