<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    const UPDATED_AT = null;

    public bool $skipObserver = false; // ← tambah ini

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

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredients::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isEntry(): bool
    {
        return strtoupper($this->type) === 'IN';
    }

    public function isExit(): bool
    {
        return strtoupper($this->type) === 'OUT';
    }
}