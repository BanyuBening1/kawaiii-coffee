<?php

namespace App\Helpers;

use Carbon\Carbon;
use InvalidArgumentException;

class PeriodHelper
{
    /**
     * Return [startDate, endDate] berdasarkan period string.
     * 
     * @param string $period  daily|weekly|monthly|yearly
     * @param string|null $date  tanggal referensi (default: hari ini)
     * @return array{Carbon, Carbon}
     */
    public static function getDateRange(string $period, ?string $date = null): array
    {
        $reference = $date ? Carbon::parse($date) : Carbon::today();

        return match ($period) {
            'daily'   => [$reference->copy()->startOfDay(), $reference->copy()->endOfDay()],
            'weekly'  => [$reference->copy()->startOfWeek(), $reference->copy()->endOfWeek()],
            'monthly' => [$reference->copy()->startOfMonth(), $reference->copy()->endOfMonth()],
            'yearly'  => [$reference->copy()->startOfYear(), $reference->copy()->endOfYear()],
            default   => throw new InvalidArgumentException("Period '{$period}' tidak valid."),
        };
    }

    /**
     * Return format GROUP BY berdasarkan period untuk query chart.
     */
    public static function getGroupByFormat(string $period): string
    {
        return match ($period) {
            'daily'   => '%H:00',       // per jam
            'weekly'  => '%Y-%m-%d',    // per hari
            'monthly' => '%Y-%m-%d',    // per hari
            'yearly'  => '%Y-%m',       // per bulan
            default   => '%Y-%m-%d',
        };
    }
}