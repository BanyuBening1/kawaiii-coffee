<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredients;
use App\Models\StockMovement;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class IngredientsController extends Controller
{
    public function index()
    {
        return response()->json(Ingredients::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ingredients,name',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required',
        ]);

        $ingredient = Ingredients::create($request->all());

        // =========================
        // LOG AUDIT (CREATE)
        // =========================
        AuditLogService::create(
            'ingredients',
            'Menambah bahan ' . $ingredient->name,
            $ingredient->id,
            $ingredient->toArray()
        );

        return response()->json($ingredient, 201);
    }

    public function show($id)
    {
        $ingredient = Ingredients::findOrFail($id);
        return response()->json($ingredient);
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredients::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:ingredients,name,' . $id,
            'stock' => 'required|numeric|min:0',
            'unit' => 'required',
        ]);

        // Capture original data BEFORE update
        $oldData = $ingredient->getOriginal();
        
        $ingredient->update($request->all());

        // =========================
        // LOG AUDIT (UPDATE)
        // =========================
        AuditLogService::update(
            'ingredients',
            'Mengubah bahan ' . $ingredient->name,
            $ingredient->id,
            $oldData,
            $ingredient->toArray()
        );

        return response()->json($ingredient);
    }
    
    public function destroy($id)
    {
        $ingredient = Ingredients::findOrFail($id);
        // Capture data before deletion
        $ingredientData = $ingredient->toArray();
        
        $ingredient->delete();

        // =========================
        // LOG AUDIT (DELETE)
        // =========================
        AuditLogService::delete(
            'ingredients',
            'Menghapus bahan ' . $ingredientData['name'],
            $id,
            $ingredientData
        );

        return response()->json(['message' => 'Deleted']);
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
            'description' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request, $id) {
            $ingredient = Ingredients::findOrFail($id);

            // ➜ tambah stok
            $ingredient->stock += $request->quantity;
            $ingredient->save();

            // ➜ simpan ke stock movement
            StockMovement::create([
                'user_id' => auth()->id(),
                'ingredient_id' => $ingredient->id,
                'type' => 'IN',
                'quantity' => $request->quantity,
                'reference' => 'RESTOCK',
                'description' => $request->description,
            ]);

            // =========================
            // LOG AUDIT (CREATE)
            // =========================
            AuditLogService::create(
                'ingredients',
                'Restock bahan ' . $ingredient->name . ' sebanyak ' . $request->quantity . ' ' . $ingredient->unit,
                $ingredient->id,
                $ingredient->toArray()
            );

            // =========================
            // NOTIFIKASI RESTOCK
            // =========================
            $admin = \App\Models\User::where('role_id', 1)->first();
            if ($admin) {
                notify(
                    'Restock Bahan',
                    'Stok ' . $ingredient->name . ' bertambah ' . $request->quantity . ' ' . $ingredient->unit,
                    'restock',
                    'ingredient_' . $ingredient->id,
                    $admin->id
                );
            }

            $warning = null;
            if ($ingredient->stock < $ingredient->min_stock) {
                $warning = 'Stok masih di bawah minimum!';
            }

            return response()->json([
                'message' => 'Restock berhasil',
                'warning' => $warning,
                'data' => $ingredient
            ]);
        });
    }
}
