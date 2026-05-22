<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Ingredients;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    // =========================
    // GET ALL STOCK MOVEMENTS
    // =========================
    public function index(Request $request)
    {
        $query = StockMovement::with(['ingredient', 'user'])->latest();

        if ($request->ingredient_id) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        if ($request->type) {
            $type = strtoupper($request->type);
            if (in_array($type, ['IN', 'OUT'])) {
                $query->where('type', $type);
            }
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        return response()->json($query->paginate(10));
    }

    // =========================
    // SHOW DETAIL
    // =========================
    public function show($id)
    {
        $movement = StockMovement::with(['ingredient', 'user'])->findOrFail($id);
        return response()->json($movement);
    }

    // =========================
    // MANUAL ADJUSTMENT
    // =========================
    public function adjust(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'type'          => 'required|in:IN,OUT',
            'quantity'      => 'required|numeric|min:1',
            'description'   => 'required|string',
        ]);

        $userId = auth()->id();

        return DB::transaction(function () use ($request, $userId) {

            $ingredient = Ingredients::findOrFail($request->ingredient_id);
            $oldData    = $ingredient->toArray();

            // Validasi stok
            if ($request->type === 'OUT' && $ingredient->stock < $request->quantity) {
                return response()->json(['message' => 'Stok tidak cukup'], 400);
            }

            // Simpan movement — Observer otomatis update stok
            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type'          => $request->type,
                'quantity'      => $request->quantity,
                'reference'     => 'MANUAL_ADJUST',
                'description'   => $request->description,
                'user_id'       => $userId,
            ]);

            // Refresh untuk dapat stok terbaru setelah observer jalan
            $ingredient->refresh();

            // Audit log
            $action = $request->type === 'IN'
                ? 'Penambahan stok manual'
                : 'Pengurangan stok manual';

            AuditLogService::update(
                'ingredients',
                $action . ' - ' . $ingredient->name . ': ' . $request->description,
                $ingredient->id,
                $oldData,
                $ingredient->toArray()
            );

            return response()->json([
                'message' => 'Adjustment berhasil',
                'data'    => $ingredient,
            ]);
        });
    }
}