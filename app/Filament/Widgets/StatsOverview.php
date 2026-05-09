<?php

namespace App\Filament\Widgets;

use App\Models\Ingredients;
use App\Models\Products;
use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1; // tampil sebelum chart

    protected function getStats(): array
    {
        $today = now()->toDateString();

        // Penjualan hari ini
        $todaySales = Transactions::where('status', 'paid')
            ->whereDate('transaction_date', $today)
            ->sum('total');

        // Transaksi hari ini
        $todayCount = Transactions::where('status', 'paid')
            ->whereDate('transaction_date', $today)
            ->count();

        // Produk aktif
        $activeProducts = Products::where('is_active', true)->count();

        // Stok menipis (di bawah min_stock)
        $lowStock = Ingredients::whereColumn('stock', '<=', 'min_stock')->count();

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todaySales, 0, ',', '.'))
                ->description('Total transaksi lunas hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')  // ← ini yang kasih warna hijau
                ->icon('heroicon-o-banknotes')
                ->chart([0, 0, 0, 0, 0, $todaySales]), // ← tambah mini chart

            Stat::make('Transaksi Hari Ini', $todayCount . ' transaksi')
                ->description('Jumlah order masuk hari ini')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('success') // ← ganti dari 'info' ke 'success'
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Produk Aktif', $activeProducts . ' produk')
                ->description('Produk yang tersedia di menu')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Stok Menipis', $lowStock . ' bahan')
                ->description('Bahan baku di bawah stok minimum')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStock > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-beaker'),
        ];
    }
}