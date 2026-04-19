<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transactions;
use App\Models\DetailTransaction;
use App\Models\Products;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $cashier = User::first();
            $products = Products::all();

            if ($products->count() == 0) {
                throw new \Exception('Product kosong, isi dulu product!');
            }

            // buat 10 transaksi (1 produk per transaksi)
            for ($i = 0; $i < 10; $i++) {

                // ambil 1 produk random
                $product = $products->random();

                $qty = rand(1, 3);
                $price = $product->selling_price;
                $cost = $product->cost_price;

                $subtotal = $price * $qty;
                $total = $subtotal;

                $paid = $total + rand(1000, 5000);
                $change = $paid - $total;

                // 🧾 buat transaksi
                $transaction = Transactions::create([
                    'transaction_code' => 'TRX-' . strtoupper(Str::random(6)),
                    'transaction_date' => now()->subDays(rand(0, 7)),
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'paid_amount' => $paid,
                    'change_amount' => $change,
                    'payment_method' => collect(['cash', 'qris', 'transfer'])->random(),
                    'status' => 'paid',
                    'cashier_id' => $cashier->id,
                ]);

                // 📦 buat detail (hanya 1)
                DetailTransaction::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();

            $this->command->info('Seeder transaksi (1 produk) berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error($e->getMessage());
        }
    }
}