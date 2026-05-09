<?php

namespace App\Filament\Widgets;

use App\Models\DetailTransaction;
use App\Models\Transactions;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class TotalOmzet extends Widget
{
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.total-omzet';
    protected static ?string $pollingInterval = null;   

    public function getOmzetBulanIni(): string
    {
        return number_format(
            Transactions::where('status', 'paid')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('total'),
            0, ',', '.'
        );
    }

    public function getProfitBulanIni(): string
    {
        return number_format(
            DetailTransaction::whereHas('transaction', fn ($q) =>
                $q->where('status', 'paid')
                  ->whereMonth('transaction_date', now()->month)
                  ->whereYear('transaction_date', now()->year)
            )->sum(DB::raw('(unit_price - unit_cost) * quantity')),
            0, ',', '.'
        );
    }

    public function getOmzetTotal(): string
    {
        return number_format(
            Transactions::where('status', 'paid')->sum('total'),
            0, ',', '.'
        );
    }

    public function getContentClass(): string
    {
        return '!p-0 !shadow-none !bg-transparent !border-0';
    }
}