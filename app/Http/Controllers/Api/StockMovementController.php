<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    // =========================
    // GET ALL STOCK MOVEMENTS
    // =========================
    public function index(Request $request)
    {
        $query = StockMovement::with(['ingredient', 'user'])
            ->latest();

        // 🔍 filter by ingredient
        if ($request->ingredient_id) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        // 🔍 filter by type (IN / OUT)
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // 🔍 filter by tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        return response()->json($query->paginate(10));
    }

    // =========================
    // SHOW DETAIL
    // =========================
    public function show($id)
    {
        $movement = StockMovement::with(['ingredient', 'user'])
            ->findOrFail($id);

        return response()->json($movement);
    }

    // =========================
    // MANUAL ADJUSTMENT (OPSIONAL 🔥)
    // =========================
    public function adjust(Request $request)
    {
        $request->validate([
            'user_id' => auth()->id(),
            'ingredient_id' => 'required|exists:ingredients,id',
            'type' => 'required|in:IN,OUT',
            'quantity' => 'required|numeric|min:1',
            'description' => 'required|string'
        ]);

        $ingredient = \App\Models\Ingredients::findOrFail($request->ingredient_id);

        // ⚠️ kalau OUT, cek stok cukup
        if ($request->type === 'OUT' && $ingredient->stock < $request->quantity) {
            return response()->json([
                'error' => 'Stok tidak cukup untuk pengurangan'
            ], 400);
        }

        \DB::transaction(function () use ($request, $ingredient) {

            // Capture old stock before adjustment
            $oldData = $ingredient->toArray();

            // ➜ update stok
            if ($request->type === 'IN') {
                $ingredient->stock += $request->quantity;
            } else {
                $ingredient->stock -= $request->quantity;
            }

            $ingredient->save();

            // ➜ simpan movement
            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reference' => 'MANUAL_ADJUST',
                'description' => $request->description,
                'user_id' => auth()->id(),
            ]);

            // =========================
            // LOG AUDIT
            // =========================
            $action = $request->type === 'IN' ? 'Penambahan stok manual' : 'Pengurangan stok manual';
            AuditLogService::update(
                'ingredients',
                $action . ' untuk ' . $ingredient->name . ': ' . $request->description,
                $ingredient->id,
                $oldData,
                $ingredient->toArray()
            );
        });

        return response()->json([
            'message' => 'Adjustment berhasil'
        ]);
    }
}