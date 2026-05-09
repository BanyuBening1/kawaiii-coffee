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
    protected string  $color     = 'gray';

    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    public    ?string     $filter     = '30';

    protected function getFilters(): ?array
    {
        return [
            '7'   => '7 Hari Terakhir',
            '14'  => '14 Hari Terakhir',
            '30'  => '30 Hari Terakhir',
            '60'  => '2 Bulan Terakhir',
            '90'  => '3 Bulan Terakhir',
            '180' => '6 Bulan Terakhir',
            '365' => '1 Tahun Terakhir',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;

        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate   = now()->endOfDay();

        $dateRange = $this->generateDateRange($startDate, $endDate);
        $rawData   = $this->fetchSalesData($startDate, $endDate);

        $sales  = $this->mapToDateRange($dateRange, $rawData, 'total_sales');
        $profit = $this->mapToDateRange($dateRange, $rawData, 'total_profit');

        // Label lebih ringkas untuk range panjang
        $labels = $dateRange->map(function (Carbon $d) use ($days) {
            if ($days <= 14) return $d->translatedFormat('d M');
            if ($days <= 90) return $d->translatedFormat('d M');
            return $d->translatedFormat('d M Y');
        });

        return [
            'datasets' => [
                [
                    'label'                => 'Total Penjualan',
                    'data'                 => $sales->values()->toArray(),
                    'borderColor'          => '#6366f1', // Indigo — eye-catching, pro look
                    'backgroundColor'      => 'rgba(99, 102, 241, 0.06)',
                    'fill'                 => 'start',
                    'tension'              => 0.4,
                    'borderWidth'          => 2.5,
                    'pointRadius'          => $days <= 14 ? 5 : 0,
                    'pointHitRadius'       => 20,
                    'pointHoverRadius'     => 6,
                    'pointBackgroundColor' => '#6366f1',
                ],
                [
                    'label'                => 'Profit Bersih',
                    'data'                 => $profit->values()->toArray(),
                    'borderColor'          => '#10b981', // Emerald — kontras bagus dengan indigo
                    'backgroundColor'      => 'rgba(16, 185, 129, 0.06)',
                    'fill'                 => 'start',
                    'tension'              => 0.4,
                    'borderWidth'          => 2.5,
                    'pointRadius'          => $days <= 14 ? 5 : 0,
                    'pointHitRadius'       => 20,
                    'pointHoverRadius'     => 6,
                    'pointBackgroundColor' => '#10b981',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        $days = (int) $this->filter;

        $maxTicks = match (true) {
            $days <= 7   => 7,
            $days <= 14  => 7,
            $days <= 30  => 10,
            $days <= 90  => 12,
            $days <= 180 => 12,
            default      => 13,
        };

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
                    'align'    => 'end',
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
                    'backgroundColor' => 'rgba(17, 24, 39, 0.9)',
                    'titleFont'       => ['size' => 13, 'weight' => '600'],
                    'bodyFont'        => ['size' => 12],
                    'cornerRadius'    => 8,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => [
                        'font'          => ['size' => 11],
                        'color'         => '#9ca3af',
                        'maxTicksLimit' => $maxTicks,
                        'maxRotation'   => 0,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid'        => [
                        'color'      => 'rgba(156, 163, 175, 0.08)',
                        'drawBorder' => false,
                    ],
                    'ticks'       => [
                        'font'          => ['size' => 11],
                        'color'         => '#9ca3af',
                        'maxTicksLimit' => 6,
                        'callback'      => "callback(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            return 'Rp ' + value;
                        }",
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

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