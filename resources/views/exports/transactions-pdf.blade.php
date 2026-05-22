
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }

        /* Header */
        .header { text-align: center; padding: 16px 0 12px; border-bottom: 3px solid #1a6b45; margin-bottom: 16px; }
        .header h1 { font-size: 22px; color: #1a6b45; font-weight: bold; letter-spacing: 1px; }
        .header .subtitle { color: #666; font-size: 11px; margin-top: 4px; }

        /* Meta */
        .meta { display: flex; justify-content: space-between; margin-bottom: 14px; font-size: 10px; color: #888; border-bottom: 1px dashed #ddd; padding-bottom: 8px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-title { font-size: 12px; font-weight: bold; color: #1a6b45; margin-bottom: 6px; border-left: 4px solid #1a6b45; padding-left: 8px; }
        thead tr { background-color: #1a6b45; color: white; }
        thead th { padding: 7px 10px; text-align: left; font-size: 10px; font-weight: bold; }
        tbody tr:nth-child(even) { background-color: #f3faf6; }
        tbody tr:nth-child(odd) { background-color: #ffffff; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }

        /* Badge */
        .badge { padding: 2px 7px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-paid      { background: #dcfce7; color: #166534; }
        .badge-pending   { background: #fef9c3; color: #854d0e; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        /* Summary */
        .summary-wrap { display: flex; justify-content: space-between; gap: 20px; margin-top: 4px; }
        .summary-box { flex: 1; }
        .summary-box table { margin-bottom: 0; }
        .summary-box td { padding: 5px 10px; font-size: 10px; }
        .summary-box .lbl { color: #555; }
        .summary-box .val { font-weight: bold; text-align: right; }
        .total-row td { background: #1a6b45 !important; color: white !important; font-weight: bold; padding: 6px 10px; }

        /* Best Seller */
        .bs-rank { font-weight: bold; color: #1a6b45; text-align: center; }
        .bs-medal-1 { color: #d97706; }
        .bs-medal-2 { color: #6b7280; }
        .bs-medal-3 { color: #92400e; }

        /* Footer */
        .footer { margin-top: 24px; text-align: center; font-size: 9px; color: #bbb; border-top: 1px dashed #ddd; padding-top: 10px; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>Kawaiii Coffee</h1>
        <div class="subtitle">Laporan Transaksi &mdash; {{ $period }}</div>
    </div>

    {{-- Meta --}}
    <div class="meta">
        <span>Dicetak: {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span>
        <span>Total Data: {{ count($transactions) }} transaksi</span>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="table-title">Daftar Transaksi</div>
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:18%">Kode Transaksi</th>
                <th style="width:20%">Tanggal</th>
                <th style="width:14%">Kasir</th>
                <th style="width:13%">Pembayaran</th>
                <th style="width:11%">Status</th>
                <th style="width:20%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $trx)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $trx->transaction_code }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                    <td>{{ $trx->cashier?->name ?? '-' }}</td>
                    <td>{{ ucfirst($trx->payment_method) }}</td>
                    <td>
                        @php
                            $badgeClass  = match($trx->status) { 'paid' => 'badge-paid', 'pending' => 'badge-pending', 'cancelled' => 'badge-cancelled', default => '' };
                            $statusLabel = match($trx->status) { 'paid' => 'Lunas', 'pending' => 'Pending', 'cancelled' => 'Dibatalkan', default => $trx->status };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td style="text-align:right;">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:16px; color:#999;">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary + Best Seller --}}
    <div class="summary-wrap">

        {{-- Ringkasan --}}
        <div class="summary-box">
            <div class="table-title" style="margin-bottom:6px;">Ringkasan</div>
            <table>
                <tr>
                    <td class="lbl">Total Transaksi</td>
                    <td class="val">{{ count($transactions) }}</td>
                </tr>
                <tr>
                    <td class="lbl">Transaksi Lunas</td>
                    <td class="val">{{ collect($transactions)->where('status', 'paid')->count() }}</td>
                </tr>
                <tr>
                    <td class="lbl">Transaksi Pending</td>
                    <td class="val">{{ collect($transactions)->where('status', 'pending')->count() }}</td>
                </tr>
                <tr>
                    <td class="lbl">Transaksi Dibatalkan</td>
                    <td class="val">{{ collect($transactions)->where('status', 'cancelled')->count() }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total Omzet</td>
                    <td style="text-align:right;">Rp {{ number_format(collect($transactions)->where('status', 'paid')->sum('total'), 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>


    </div>

    <div class="footer">
        Kawaii Café &mdash; Dokumen ini digenerate otomatis oleh sistem &mdash; {{ now()->format('Y') }}
    </div>

</body>
</html>