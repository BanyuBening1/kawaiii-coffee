<?php

namespace App\Observers;

use App\Models\Ingredients;
use App\Models\StockMovement;

class StockMovementObserver
{
    public function created(StockMovement $stockMovement): void
    {
        if ($stockMovement->skipObserver) return; // ← skip kalau dari transaksi

        $ingredient = Ingredients::find($stockMovement->ingredient_id);

        if (!$ingredient) return;

        if (strtoupper($stockMovement->type) === 'IN') {
            $ingredient->increment('stock', $stockMovement->quantity);
        } elseif (strtoupper($stockMovement->type) === 'OUT') {
            $ingredient->decrement('stock', $stockMovement->quantity);
        }
    }
}