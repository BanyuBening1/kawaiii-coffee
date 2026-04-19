<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Models\DetailTransaction;
use App\Models\Products;
use App\Models\ProductIngredients;
use App\Models\Ingredients;
use App\Models\StockMovement;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionsController extends Controller
{
    // =========================
    // GET ALL TRANSACTIONS
    // =========================
    public function index(Request $request)
    {
        $query = Transactions::with(['cashier', 'details.product'])
            ->latest();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        if ($request->cashier_id) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        return response()->json($query->paginate(10));
    }

    // =========================
    // STORE TRANSACTION (INTI)
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => auth()->id(),
            'payment_method' => 'required|in:cash,qris,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {

            $subtotal = 0;
            $details = [];

            // =========================
            // 1. VALIDASI + HITUNG
            // =========================
            foreach ($request->items as $item) {

                $product = Products::findOrFail($item['product_id']);
                $qty = $item['quantity'];

                $price = $product->selling_price;
                $cost = $product->cost_price;

                $itemSubtotal = $price * $qty;
                $subtotal += $itemSubtotal;

                // 🔥 VALIDASI STOK BAHAN
                $recipes = ProductIngredients::where('product_id', $product->id)->get();

                foreach ($recipes as $recipe) {
                    $ingredient = Ingredients::find($recipe->ingredient_id);

                    $needed = $recipe->quantity * $qty;

                    if ($ingredient->stock < $needed) {
                        throw new \Exception("Stok bahan '{$ingredient->name}' tidak cukup untuk {$product->product_name}");
                    }
                }

                $details[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'unit_cost' => $cost,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $total = $subtotal;

            // =========================
            // 2. VALIDASI PEMBAYARAN
            // =========================
            if ($request->paid_amount < $total) {
                throw new \Exception("Uang tidak cukup");
            }

            $change = $request->paid_amount - $total;

            // =========================
            // 3. SIMPAN TRANSAKSI
            // =========================
            $transaction = Transactions::create([
                'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
                'transaction_date' => now(),
                'subtotal' => $subtotal,
                'total' => $total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $change,
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'cashier_id' => auth()->id(),
            ]);

            // =========================
            // 4. SIMPAN DETAIL
            // =========================
            foreach ($details as $detail) {
                $detail['transaction_id'] = $transaction->id;
                DetailTransaction::create($detail);
            }

            // =========================
            // 5. 🔥 KURANGI STOK + CATAT
            // =========================
            $admin = \App\Models\User::where('role_id', 1)->first();
            
            foreach ($details as $detail) {

                $recipes = ProductIngredients::where('product_id', $detail['product_id'])->get();

                foreach ($recipes as $recipe) {

                    $ingredient = Ingredients::find($recipe->ingredient_id);

                    $used = $recipe->quantity * $detail['quantity'];

                    // ➜ kurangi stok
                    $ingredient->stock -= $used;
                    $ingredient->save();

                    // ➜ simpan stock movement
                    StockMovement::create([
                        'ingredient_id' => $ingredient->id,
                        'user_id' => auth()->id(),
                        'type' => 'OUT',
                        'quantity' => $used,
                        'reference' => $transaction->transaction_code,
                        'description' => 'Penggunaan bahan dari transaksi',
                        // 'user_id' => auth()->id(), // kalau kamu pakai
                    ]);

                    // ⚠️ Cek stok minimum dan buat notifikasi jika diperlukan
                    if ($admin && $ingredient->stock < $ingredient->min_stock) {
                        // Cek apakah sudah ada notifikasi low_stock untuk ingredient ini hari ini
                        $existingNotification = \App\Models\Notification::where('user_id', $admin->id)
                            ->where('type', 'low_stock')
                            ->where('reference', 'ingredient_' . $ingredient->id)
                            ->whereDate('created_at', now()->toDateString())
                            ->exists();

                        if (!$existingNotification) {
                            notify(
                                'Stok Menipis',
                                'Stok ' . $ingredient->name . ' hampir habis (sisa: ' . $ingredient->stock . ' ' . $ingredient->unit . ')',
                                'low_stock',
                                'ingredient_' . $ingredient->id,
                                $admin->id
                            );
                        }
                    }
                }
            }

            // =========================
            // 6. NOTIFIKASI TRANSAKSI
            // =========================
            if ($admin) {
                notify(
                    'Transaksi Baru',
                    'Transaksi ' . $transaction->transaction_code . ' berhasil dengan total Rp ' . number_format($transaction->total, 0, ',', '.'),
                    'transaction',
                    'transaction_' . $transaction->id,
                    $admin->id
                );
            }

            // =========================
            // 7. LOG AUDIT
            // =========================
            AuditLogService::create(
                'transactions',
                'Membuat transaksi ' . $transaction->transaction_code,
                $transaction->id,
                $transaction->toArray()
            );

            // =========================
            // 8. RETURN RESPONSE
            // =========================
            return response()->json([
                'message' => 'Transaksi berhasil',
                'data' => $transaction->load('details.product')
            ]);
        });
    }

    // =========================
    // SHOW DETAILdasd
    // =========================
    public function show($id)
    {
        $transaction = Transactions::with([
            'cashier',
            'details.product'
        ])->findOrFail($id);

        return response()->json($transaction);
    }
}