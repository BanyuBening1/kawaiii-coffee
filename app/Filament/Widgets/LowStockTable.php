<?php

namespace App\Filament\Widgets;

use App\Models\Ingredients;
use Filament\Widgets\Widget;

class LowStockTable extends Widget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 1;
    protected string $view = 'filament.widgets.low-stock-table';

    public function getLowStock()
    {
        return Ingredients::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->take(7)
            ->get();
    }
}