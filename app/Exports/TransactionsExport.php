<?php

namespace App\Exports;

use App\Models\Transactions;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        protected Collection $transactions,
        protected string $period,
    ) {}

    public function title(): string
    {
        return 'Transaksi';
    }

    public function collection(): Collection
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            '#',
            'Kode Transaksi',
            'Tanggal',
            'Kasir',
            'Metode Pembayaran',
            'Status',
            'Total',
            'Dibayar',
            'Kembalian',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->transaction_code,
            \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y H:i'),
            $row->cashier?->name ?? '-',
            ucfirst($row->payment_method),
            match($row->status) {
                'paid'      => 'Lunas',
                'pending'   => 'Pending',
                'cancelled' => 'Dibatalkan',
                default     => $row->status,
            },
            $row->total,
            $row->paid_amount,
            $row->change_amount,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1a6b45'],
                ],
            ],
        ];
    }
}