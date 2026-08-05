<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetailTransaction;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'categories_id',
        'selling_price',
        'cost_price',
        'is_active',
        'image',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function getMarginAttribute(): float
    {
        return (float) $this->selling_price - (float) $this->cost_price;
    }

    // 🟢 1. Relasi HasMany ke Model Pivot ProductIngredients (Penting untuk Eager Loading productIngredients.ingredient)
    public function productIngredients(): HasMany
    {
        return $this->hasMany(ProductIngredients::class, 'product_id');
    }

    // 🟢 2. Relasi BelongsToMany Direct ke Ingredients
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredients::class, 'product_ingredients', 'product_id', 'ingredient_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function details()
    {
        return $this->hasMany(DetailTransaction::class, 'product_id');
    }
}