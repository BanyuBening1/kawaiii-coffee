<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Services\AuditLogService;
use App\Services\PaymentService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionsController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService,
        protected PaymentService $paymentService,
    ) {}

    // =========================
    // GET ALL TRANSACTIONS
    // =========================
    public function index(Request $request)
    {
        $query = Transactions::with(['cashier', 'details.product'])->latest();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
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
    // STORE — Cash/QRIS/Transfer
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'payment_method'     => 'required|in:cash,qris,transfer',
            'paid_amount'        => 'required|numeric|min:0',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $calculated = $this->transactionService->calculateItems($request->items);

            if ($request->paid_amount < $calculated['subtotal']) {
                throw new \Exception("Uang tidak cukup");
            }

            $transaction = $this->transactionService->createTransaction([
                'subtotal'       => $calculated['subtotal'],
                'paid_amount'    => $request->paid_amount,
                'change_amount'  => $request->paid_amount - $calculated['subtotal'],
                'payment_method' => $request->payment_method,
                'status'         => 'paid',
                'cashier_id'     => auth()->id(),
            ]);

            $this->transactionService->saveDetails($transaction, $calculated['details']);
            $this->transactionService->deductStock($transaction, $calculated['details']);
            $this->transactionService->sendNotifications($transaction);

            AuditLogService::create(
                'transactions',
                'Membuat transaksi ' . $transaction->transaction_code,
                $transaction->id,
                $transaction->toArray()
            );

            return response()->json([
                'message' => 'Transaksi berhasil',
                'data'    => $transaction->load('details.product'),
            ]);
        });
    }

    // =========================
    // INITIATE — Snap Token
    // =========================
    public function initiate(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $calculated = $this->transactionService->calculateItems($request->items);

            $transaction = $this->transactionService->createTransaction([
                'subtotal'       => $calculated['subtotal'],
                'paid_amount'    => 0,
                'change_amount'  => 0,
                'payment_method' => 'midtrans',
                'status'         => 'pending',
                'cashier_id'     => auth()->id(),
            ]);

            $this->transactionService->saveDetails($transaction, $calculated['details']);

            $snapToken = $this->paymentService->createSnapToken(
                $transaction,
                $calculated['itemDetails'],
                auth()->user()
            );

            return response()->json([
                'message'          => 'Transaksi dibuat, lanjutkan pembayaran',
                'transaction_code' => $transaction->transaction_code,
                'snap_token'       => $snapToken,
                'client_key'       => env('MIDTRANS_CLIENT_KEY'),
                'total'            => $calculated['subtotal'],
            ]);
        });
    }

    // =========================
    // INITIATE QRIS — Core API
    // =========================
    public function initiateQris(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $calculated = $this->transactionService->calculateItems($request->items);

            $transaction = $this->transactionService->createTransaction([
                'subtotal'       => $calculated['subtotal'],
                'paid_amount'    => 0,
                'change_amount'  => 0,
                'payment_method' => 'qris',
                'status'         => 'pending',
                'cashier_id'     => auth()->id(),
            ]);

            $this->transactionService->saveDetails($transaction, $calculated['details']);

            $qrUrl = $this->paymentService->createQris(
                $transaction,
                $calculated['itemDetails']
            );

            return response()->json([
                'message'          => 'QRIS berhasil dibuat',
                'transaction_id'   => $transaction->id,        // ← tambah ini
                'transaction_code' => $transactionCode,
                'qr_url'           => $qrUrl,
                'total'            => $subtotal,
                'expired_at'       => now()->addMinutes(15)->toDateTimeString(),
            ]);
        });
    }

    // =========================
    // MIDTRANS CALLBACK
    // =========================
    public function midtransCallback(Request $request)
    {
        try {
            Log::info('Midtrans callback received', $request->all());

            $result      = $this->paymentService->handleCallback($request->all());
            $orderId     = $request->input('order_id');
            $transaction = Transactions::where('transaction_code', $orderId)->first();

            if ($result === 'paid' && $transaction) {
                $details = $transaction->details->map(fn ($d) => [
                    'product_id' => $d->product_id,
                    'quantity'   => $d->quantity,
                    'unit_price' => $d->unit_price,
                    'unit_cost'  => $d->unit_cost,
                    'subtotal'   => $d->subtotal,
                ])->toArray();

                $this->transactionService->deductStock($transaction, $details);
                $this->transactionService->sendNotifications($transaction);

                AuditLogService::create(
                    'transactions',
                    'Pembayaran Midtrans dikonfirmasi: ' . $orderId,
                    $transaction->id,
                    $transaction->toArray()
                );
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans callback error: ' . $e->getMessage());
            $code = $e->getCode() === 403 ? 403 : 500;
            return response()->json(['message' => 'Error'], $code);
        }
    }

    // =========================
    // SHOW DETAIL
    // =========================
    public function show($id)
    {
        $transaction = Transactions::with(['cashier', 'details.product'])->findOrFail($id);
        return response()->json($transaction);
    }

    public function checkStatus($id)
    {
        $transaction = Transactions::where('transaction_code', $id)
            ->select('transaction_code', 'status', 'payment_method', 'total', 'updated_at')
            ->firstOrFail();

        return response()->json([
            'message' => 'OK',
            'data'    => $transaction,
        ]);
    }
}