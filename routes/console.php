<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Transactions;
use App\Models\Notification;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Weekly sales report notification
Schedule::call(function () {
    $sevenDaysAgo = Carbon::now()->subDays(7);

    $transactions = Transactions::where('transaction_date', '>=', $sevenDaysAgo)->get();

    $totalTransactions = $transactions->count();
    $totalRevenue = $transactions->sum('total');

    if ($totalTransactions > 0) {

        $exists = Notification::where('type', 'weekly_report')
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if ($exists) return;

        $formattedRevenue = number_format($totalRevenue, 0, ',', '.');

        notify(
            title: 'Laporan Mingguan',
            message: "Penjualan minggu ini: {$totalTransactions} transaksi, omzet Rp {$formattedRevenue}",
            type: 'weekly_report',
            userId: 1
        );
    }
})->weeklyOn(1, '08:00');   
