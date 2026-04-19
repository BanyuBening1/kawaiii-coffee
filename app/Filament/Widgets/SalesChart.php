<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected ?string $heading   = 'Analisis Penjualan & Profit';
    protected ?string $maxHeight = '320px';
    
    // Menggunakan warna abu-abu agar tidak bertabrakan dengan garis chart
    protected string  $color     = 'gray'; 

    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    public    ?string     $filter     = '30';

    protected function getFilters(): ?array
    {
        return [
            '7'  => '7 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            '90' => '3 Bulan Terakhir',
        ];
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            '7'     => 7,
            '90'    => 90,
            default => 30,
        };

        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate   = now()->endOfDay();

        $dateRange = $this->generateDateRange($startDate, $endDate);
        $rawData   = $this->fetchSalesData($startDate, $endDate);

        $sales  = $this->mapToDateRange($dateRange, $rawData, 'total_sales');
        $profit = $this->mapToDateRange($dateRange, $rawData, 'total_profit');
        $labels = $dateRange->map(fn (Carbon $d) => $d->translatedFormat('d M'));

        return [
            'datasets' => [
                [
                    'label'           => 'Total Penjualan',
                    'data'            => $sales->values()->toArray(),
                    // Blue Electric Modern
                    'borderColor'     => '#3b82f6', 
                    'backgroundColor' => 'rgba(59, 130, 246, 0.05)',
                    'fill'            => 'start',
                    'tension'         => 0.4,
                    'borderWidth'     => 3,
                    'pointRadius'     => $days <= 7 ? 5 : 0, // Sembunyikan titik jika data terlalu padat
                    'pointHitRadius'  => 20,
                    'pointHoverRadius'=> 6,
                    'pointBackgroundColor' => '#3b82f6',
                ],
                [
                    'label'           => 'Profit Bersih',
                    'data'            => $profit->values()->toArray(),
                    // Emerald Green Modern
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill'            => 'start',
                    'tension'         => 0.4,
                    'borderWidth'     => 3,
                    'pointRadius'     => $days <= 7 ? 5 : 0,
                    'pointHitRadius'  => 20,
                    'pointHoverRadius'=> 6,
                    'pointBackgroundColor' => '#10b981',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive'          => true,
            'maintainAspectRatio' => false,
            'interaction'         => [
                'mode'      => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display'  => true,
                    'position' => 'top',
                    'align'    => 'end', // Merapikan keterangan ke pojok kanan atas
                    'labels'   => [
                        'usePointStyle' => true,
                        'pointStyle'    => 'circle',
                        'padding'       => 25,
                        'font'          => [
                            'size'   => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled'         => true,
                    'padding'         => 12,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.9)', // Dark tooltip
                    'titleFont'       => ['size' => 14],
                    'bodyFont'        => ['size' => 13],
                    'cornerRadius'    => 8,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => [
                        'font'          => ['size' => 11],
                        'color'         => '#9ca3af',
                        'maxTicksLimit' => $this->filter === '90' ? 12 : 10,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid'        => [
                        'color'           => 'rgba(156, 163, 175, 0.1)',
                        'drawBorder'      => false,
                    ],
                    'ticks'       => [
                        'font'          => ['size' => 11],
                        'color'         => '#9ca3af',
                        'maxTicksLimit' => 5,
                        // Menambahkan format mata uang singkat di sumbu Y (Opsional)
                        'callback'      => "callback(value) { 
                            if (value >= 1000000) return (value / 1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'rb';
                            return value;
                        }"
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // ... (fetchSalesData, generateDateRange, mapToDateRange tetap sama)
    
    private function fetchSalesData(Carbon $start, Carbon $end): Collection
    {
        return DB::table('detail_transactions')
            ->join('transactions', 'detail_transactions.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->select([
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(detail_transactions.unit_price * detail_transactions.quantity) as total_sales'),
                DB::raw('SUM((detail_transactions.unit_price - detail_transactions.unit_cost) * detail_transactions.quantity) as total_profit'),
            ])
            ->groupBy(DB::raw('DATE(transactions.created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');
    }

    private function generateDateRange(Carbon $start, Carbon $end): Collection
    {
        return collect(CarbonPeriod::create($start, '1 day', $end));
    }

    private function mapToDateRange(Collection $dateRange, Collection $rawData, string $field): Collection 
    {
        return $dateRange->map(function (Carbon $date) use ($rawData, $field) {
            $row = $rawData->get($date->toDateString());
            return $row ? (float) $row->{$field} : 0.0;
        });
    }
}