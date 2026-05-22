<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\TransactionsResource;
use Filament\Resources\Pages\ListRecords;
use App\Models\Transactions;
use App\Models\Products;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

// IMPORT ACTIONS UNTUK PAGE V4
use Filament\Actions\Action;
use Filament\Actions\Enums\ActionsPosition;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionsResource::class;

    /**
     * Memaksa tombol-tombol di bawah ini untuk muncul 
     * di baris bawah (sejajar di sebelah kiri Search Bar).
     */
    public function getHeaderActionsPosition(): ActionsPosition
    {
        return ActionsPosition::BeforeContent;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Ekspor Laporan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Select::make('format')
                        ->options([
                            'pdf' => 'PDF',
                            'excel' => 'Excel',
                        ])
                        ->default('pdf')
                        ->required(),

                    DatePicker::make('start_date')
                        ->required()
                        ->default(now()->startOfMonth()),

                    DatePicker::make('end_date')
                        ->required()
                        ->default(now()->endOfMonth()),

                    Select::make('status')
                        ->options([
                            '' => 'Semua',
                            'paid' => 'Lunas',
                            'pending' => 'Pending',
                            'cancelled' => 'Dibatalkan',
                        ])
                        ->default(''),
                ])
                ->action(function (array $data) {
                    $query = Transactions::with('cashier')
                        ->whereBetween('transaction_date', [
                            $data['start_date'] . ' 00:00:00',
                            $data['end_date'] . ' 23:59:59',
                        ]);

                    if (!empty($data['status'])) {
                        $query->where('status', $data['status']);
                    }

                    $transactions = $query->latest('transaction_date')->get();

                    $period = Carbon::parse($data['start_date'])->format('d M Y')
                        . ' - ' .
                        Carbon::parse($data['end_date'])->format('d M Y');

                    if ($data['format'] === 'excel') {
                        return Excel::download(
                            new TransactionsExport($transactions, $period),
                            'laporan-transaksi-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }

                    $bestSellers = Products::join('detail_transactions', 'products.id', '=', 'detail_transactions.product_id')
                        ->join('transactions', 'detail_transactions.transaction_id', '=', 'transactions.id')
                        ->whereBetween('transactions.transaction_date', [
                            $data['start_date'] . ' 00:00:00',
                            $data['end_date'] . ' 23:59:59',
                        ])
                        ->where('transactions.status', 'paid')
                        ->select(
                            'products.name',
                            DB::raw('SUM(detail_transactions.quantity) as total_sold'),
                            DB::raw('SUM(detail_transactions.subtotal) as total_revenue')
                        )
                        ->groupBy('products.id', 'products.name')
                        ->orderByDesc('total_sold')
                        ->limit(5)
                        ->get();

                    $pdf = Pdf::loadView(
                        'exports.transactions-pdf',
                        compact('transactions', 'period', 'bestSellers')
                    )->setPaper('a4', 'landscape');

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        'laporan-transaksi-' . now()->format('Y-m-d') . '.pdf'
                    );
                }),
        ];
    }
}