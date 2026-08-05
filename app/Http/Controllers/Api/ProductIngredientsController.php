<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductIngredients;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductIngredientsController extends Controller
{
    // =========================
    // GET RESEP BY PRODUCT
    // =========================
    public function index($productId)
    {
        $product = Products::with('productIngredients.ingredient')
            ->findOrFail($productId);

        return response()->json([
            'product' => $product->name, // 🟢 Disesuaikan dari product_name ke name
            'ingredients' => $product->productIngredients
        ]);
    }

    // =========================
    // ADD INGREDIENT TO PRODUCT
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        // 🔒 Hindari duplicate bahan
        $exists = ProductIngredients::where('product_id', $request->product_id)
            ->where('ingredient_id', $request->ingredient_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'error' => 'Bahan sudah ada di produk ini'
            ], 400);
        }

        $data = ProductIngredients::create($request->all());

        return response()->json([
            'message' => 'Bahan berhasil ditambahkan ke produk',
            'data' => $data
        ]);
    }

    // =========================
    // UPDATE QUANTITY
    // =========================
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01'
        ]);

        $recipe = ProductIngredients::findOrFail($id);

        $recipe->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'message' => 'Quantity berhasil diupdate',
            'data' => $recipe
        ]);
    }

    // =========================
    // DELETE RECIPE ITEM
    // =========================
    public function destroy($id)
    {
        ProductIngredients::destroy($id);

        return response()->json([
            'message' => 'Bahan berhasil dihapus dari produk'
        ]);
    }

    // =========================
    // 🔥 BULK SET RESEP (RECOMMENDED)
    // =========================
    public function bulkStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            // 🔥 Hapus resep lama (biar replace, bukan nambah terus)
            ProductIngredients::where('product_id', $request->product_id)->delete();

            foreach ($request->ingredients as $item) {
                ProductIngredients::create([
                    'product_id' => $request->product_id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Resep berhasil di-set (replace)'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}