<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Section 1: Info Utama ───────────────────────────────────
                Section::make('Informasi Aktivitas')
                    ->description('Detail aktivitas yang tercatat di sistem.')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Dilakukan Oleh')
                            ->weight('medium'),

                        TextEntry::make('action')
                            ->label('Aksi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'CREATE' => 'success',
                                'UPDATE' => 'warning',
                                'DELETE' => 'danger',
                                default  => 'gray',
                            }),

                        TextEntry::make('table_name')
                            ->label('Tabel')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('record_id')
                            ->label('ID Record'),

                        TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime('d M Y, H:i')
                            ->columnSpanFull(),
                    ]),

                // ── Section 2: Data Lama ────────────────────────────────────
                Section::make('Data Sebelum Perubahan')
                    ->description('Kondisi data sebelum diubah.')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->schema([
                        TextEntry::make('old_data')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return '—';

                                $data = is_string($state) ? json_decode($state, true) : $state;

                                if (!is_array($data)) return '—';

                                $rows = collect($data)
                                    ->map(fn ($value, $key) => 
                                        "<tr>
                                            <td style='padding:6px 12px; font-weight:600; color:#6b7280; width:35%; border-bottom:1px solid #f3f4f6;'>
                                                " . ucwords(str_replace('_', ' ', $key)) . "
                                            </td>
                                            <td style='padding:6px 12px; border-bottom:1px solid #f3f4f6;'>
                                                " . ($value ?? '—') . "
                                            </td>
                                        </tr>"
                                    )
                                    ->implode('');

                                return "<table style='width:100%; border-collapse:collapse; font-size:0.875rem;'>$rows</table>";
                            })
                            ->html(),
                    ]),

                // ── Section 3: Data Baru ────────────────────────────────────
                Section::make('Data Sesudah Perubahan')
                    ->description('Kondisi data setelah diubah.')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->schema([
                        TextEntry::make('new_data')
                            ->label('')
                            ->columnSpanFull()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return '—';

                                $data = is_string($state) ? json_decode($state, true) : $state;

                                if (!is_array($data)) return '—';

                                $rows = collect($data)
                                    ->map(fn ($value, $key) =>
                                        "<tr>
                                            <td style='padding:6px 12px; font-weight:600; color:#6b7280; width:35%; border-bottom:1px solid #f3f4f6;'>
                                                " . ucwords(str_replace('_', ' ', $key)) . "
                                            </td>
                                            <td style='padding:6px 12px; border-bottom:1px solid #f3f4f6;'>
                                                " . ($value ?? '—') . "
                                            </td>
                                        </tr>"
                                    )
                                    ->implode('');

                                return "<table style='width:100%; border-collapse:collapse; font-size:0.875rem;'>$rows</table>";
                            })
                            ->html(),
                    ]),

            ]);
    }
}