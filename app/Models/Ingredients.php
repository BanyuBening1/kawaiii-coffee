<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredients extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'name',
        'stock',
        'unit',
        'min_stock',
    ];

    /**
     * Konversi tipe data otomatis.
     * Penting untuk tipe Decimal agar presisi saat perhitungan matematika di PHP.
     */
    protected $casts = [
        'stock'     => 'float',
        'min_stock' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────────────────────

    /**
     * Relasi ke Tabel Resep (ProductIngredients).
     * Melihat produk mana saja yang menggunakan bahan ini.
     */
    public function productIngredients(): HasMany
    {
        return $this->hasMany(ProductIngredients::class);
    }

    /**
     * Relasi Langsung ke Product (Many-to-Many melalui Resep).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'product_ingredients', 'ingredient_id', 'product_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Relasi ke Histori Stok (StockMovements).
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ─────────────────────────────────────────────────────────────
    // SCOPES & HELPER (LOGIKA BISNIS)
    // ─────────────────────────────────────────────────────────────

    /**
     * Scope untuk memfilter bahan yang stoknya di bawah batas minimum.
     * Penggunaan: Ingredient::lowStock()->get();
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= min_stock');
    }

    /**
     * Helper untuk mengecek apakah stok menipis.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}