<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    // Tabel log biasanya tidak perlu updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'ingredient_id',
        'type',
        'quantity',
        'reference',
        'description',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke Bahan Baku
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredients::class);
    }

    /**
     * Relasi ke User (yang melakukan stock movement)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk mengecek apakah stok masuk
     */
    public function isEntry(): bool
    {
        return strtoupper($this->type) === 'IN';
    }

    /**
     * Helper untuk mengecek apakah stok keluar
     */
    public function isExit(): bool
    {
        return strtoupper($this->type) === 'OUT';
    }
}