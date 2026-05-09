<?php

namespace App\Filament\Widgets;

use App\Models\Products;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BestSellerTable extends Widget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 1;
    protected string $view = 'filament.widgets.best-seller-table';

    public function getBestSellers()
    {
        return Products::withCount([
            'details as total_sold' => fn ($q) => $q->select(DB::raw('SUM(quantity)'))
        ])
        ->orderByDesc('total_sold')
        ->take(7)
        ->get();
    }
}