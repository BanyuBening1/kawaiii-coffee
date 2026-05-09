<?php

namespace App\Services;

use App\Helpers\PeriodHelper;
use App\Models\Ingredients;
use App\Models\Transactions;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ─────────────────────────────────────────────
    // 1. SALES SUMMARY
    // ─────────────────────────────────────────────
    public function getSalesSummary(string $period, ?string $date = null): array
    {
        [$start, $end] = PeriodHelper::getDateRange($period, $date);

        $result = Transactions::query()
            ->where('status', 'paid')
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('
                COUNT(*) AS total_transactions,
                COALESCE(SUM(total), 0) AS total_revenue,
                COALESCE(SUM(subtotal), 0) AS total_subtotal,
                COALESCE(AVG(total), 0) AS average_transaction
            ')
            ->first();

        return [
            'period' => $period,
            'date_range' => [
                'start' => $start->toDateTimeString(),
                'end'   => $end->toDateTimeString(),
            ],
            'total_revenue'       => (float) $result->total_revenue,
            'total_subtotal'      => (float) $result->total_subtotal,
            'total_transactions'  => (int) $result->total_transactions,
            'average_transaction' => round((float) $result->average_transaction, 2),
        ];
    }

    // ─────────────────────────────────────────────
    // 2. TODAY SALES
    // ─────────────────────────────────────────────
    public function getTodaySales(): array
    {
        $result = Transactions::query()
            ->where('status', 'paid')
            ->whereDate('transaction_date', today())
            ->selectRaw('
                COUNT(*) AS total_transactions,
                COALESCE(SUM(total), 0) AS total_revenue,
                COALESCE(SUM(subtotal), 0) AS total_subtotal,
                COALESCE(SUM(paid_amount - total), 0) AS total_change_given
            ')
            ->first();

        // Total item terjual hari ini
        $totalItems = DB::table('detail_transactions as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->where('t.status', 'paid')
            ->whereDate('t.transaction_date', today())
            ->sum('td.quantity');

        // Breakdown payment method
        $paymentBreakdown = Transactions::query()
            ->where('status', 'paid')
            ->whereDate('transaction_date', today())
            ->groupBy('payment_method')
            ->selectRaw('
                payment_method,
                COUNT(*) AS total_transactions,
                SUM(total) AS total_revenue
            ')
            ->get()
            ->map(fn ($row) => [
                'payment_method'     => $row->payment_method,
                'total_transactions' => (int) $row->total_transactions,
                'total_revenue'      => (float) $row->total_revenue,
            ]);

        return [
            'date'               => today()->toDateString(),
            'total_revenue'      => (float) $result->total_revenue,
            'total_subtotal'     => (float) $result->total_subtotal,
            'total_transactions' => (int) $result->total_transactions,
            'total_items_sold'   => (int) $totalItems,
            'payment_breakdown'  => $paymentBreakdown,
        ];
    }

    // ─────────────────────────────────────────────
    // 3. BEST SELLERS
    // ─────────────────────────────────────────────
    public function getBestSellers(
        string $period,
        ?string $date = null,
        int $limit = 10
    ): array {
        [$start, $end] = PeriodHelper::getDateRange($period, $date);

        $results = DB::table('detail_transactions as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('products as p', 'p.id', '=', 'td.product_id')
            ->where('t.status', 'paid')
            ->whereBetween('t.transaction_date', [$start, $end])
            ->groupBy(
                'p.id',
                'p.product_name',
                'p.selling_price'
            )
            ->selectRaw('
                p.id AS product_id,
                p.product_name,
                p.selling_price,
                SUM(td.quantity) AS total_quantity,
                SUM(td.subtotal) AS total_revenue,
                SUM(td.quantity * td.unit_cost) AS total_cost,
                SUM(td.subtotal) - SUM(td.quantity * td.unit_cost) AS total_profit
            ')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        return $results->map(function ($item, $index) {
            return [
                'rank'           => $index + 1,
                'product_id'     => $item->product_id,
                'product_name'   => $item->product_name,
                'selling_price'  => (float) $item->selling_price,
                'total_quantity' => (int) $item->total_quantity,
                'total_revenue'  => (float) $item->total_revenue,
                'total_cost'     => (float) $item->total_cost,
                'total_profit'   => (float) $item->total_profit,
            ];
        })->toArray();
    }

    // ─────────────────────────────────────────────
    // 4. LOW STOCK
    // ─────────────────────────────────────────────
    public function getLowStock(): array
    {
        $results = Ingredients::query()
            ->whereColumn('stock', '<=', 'min_stock')
            ->select('id', 'name', 'stock', 'min_stock', 'unit')
            ->orderBy('stock')
            ->get();

        return $results->map(function ($item) {

            $deficit = (float) $item->min_stock - (float) $item->stock;

            $stockPercent = $item->min_stock > 0
                ? round(((float) $item->stock / (float) $item->min_stock) * 100, 1)
                : 0;

            return [
                'ingredient_id' => $item->id,
                'name'          => $item->name,
                'unit'          => $item->unit,
                'current_stock' => (float) $item->stock,
                'min_stock'     => (float) $item->min_stock,
                'deficit'       => round($deficit, 2),
                'stock_percent' => $stockPercent,
            ];
        })->toArray();
    }

    // ─────────────────────────────────────────────
    // 5. SALES CHART
    // ─────────────────────────────────────────────
    public function getSalesChart(string $period, ?string $date = null): array
    {
        [$start, $end] = PeriodHelper::getDateRange($period, $date);

        $groupFormat = PeriodHelper::getGroupByFormat($period);

        $results = Transactions::query()
            ->where('status', 'paid')
            ->whereBetween('transaction_date', [$start, $end])
            ->groupByRaw("DATE_FORMAT(transaction_date, '{$groupFormat}')")
            ->selectRaw("
                DATE_FORMAT(transaction_date, '{$groupFormat}') AS label,
                COUNT(*) AS total_transactions,
                COALESCE(SUM(total), 0) AS total_revenue,
                COALESCE(SUM(subtotal), 0) AS total_subtotal
            ")
            ->orderByRaw("DATE_FORMAT(transaction_date, '{$groupFormat}')")
            ->get();

        return [
            'period' => $period,
            'date_range' => [
                'start' => $start->toDateString(),
                'end'   => $end->toDateString(),
            ],
            'chart_data' => $results->map(fn ($row) => [
                'label'              => $row->label,
                'total_revenue'      => (float) $row->total_revenue,
                'total_subtotal'     => (float) $row->total_subtotal,
                'total_transactions' => (int) $row->total_transactions,
            ])->toArray(),
        ];
    }
}