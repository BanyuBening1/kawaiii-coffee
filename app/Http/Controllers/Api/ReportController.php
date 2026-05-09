<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\BestSellersRequest;
use App\Http\Requests\Report\SalesChartRequest;
use App\Http\Requests\Report\SalesSummaryRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    // GET /api/reports/sales-summary?period=monthly
    public function salesSummary(SalesSummaryRequest $request): JsonResponse
    {
        $data = $this->reportService->getSalesSummary(
            period: $request->validated('period'),
            date:   $request->validated('date'),
        );

        return $this->successResponse($data, 'Sales summary berhasil diambil.');
    }

    // GET /api/reports/today-sales
    public function todaySales(): JsonResponse
    {
        $data = $this->reportService->getTodaySales();

        return $this->successResponse($data, 'Today sales berhasil diambil.');
    }

    // GET /api/reports/best-sellers?period=monthly&limit=10
    public function bestSellers(BestSellersRequest $request): JsonResponse
    {
        $data = $this->reportService->getBestSellers(
            period: $request->validated('period'),
            date:   $request->validated('date'),
            limit:  (int) $request->validated('limit', 10),
        );

        return $this->successResponse($data, 'Best sellers berhasil diambil.');
    }

    // GET /api/reports/low-stock
    public function lowStock(): JsonResponse
    {
        $data = $this->reportService->getLowStock();

        return $this->successResponse($data, 'Low stock ingredients berhasil diambil.');
    }

    // GET /api/reports/sales-chart?period=weekly
    public function salesChart(SalesChartRequest $request): JsonResponse
    {
        $data = $this->reportService->getSalesChart(
            period: $request->validated('period'),
            date:   $request->validated('date'),
        );

        return $this->successResponse($data, 'Sales chart berhasil diambil.');
    }

    // ─────────────────────────────────────────────
    // Helper response — bisa juga dipindah ke trait
    // ─────────────────────────────────────────────
    private function successResponse(mixed $data, string $message = 'OK'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }
}