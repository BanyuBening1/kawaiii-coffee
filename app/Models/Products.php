<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Products extends Model
{
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
        return $floatSelling = (float) $this->selling_price - (float) $this->cost_price;
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredients::class, 'product_ingredients')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
