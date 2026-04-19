<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductIngredients extends Model
{
    // Jika Anda tidak butuh timestamps di tabel pivot, set false
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    /**
     * Relasi ke Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }

    /**
     * Relasi ke Bahan Baku
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredients::class);
    }
}