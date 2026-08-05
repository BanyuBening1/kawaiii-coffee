<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductIngredients extends Model
{
    protected $table = 'product_ingredients';

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity',
    ];

    // 🟢 Relasi ke Model Ingredients (Bahan)
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredients::class, 'ingredient_id');
    }

    // 🟢 Relasi ke Model Products (Produk)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}