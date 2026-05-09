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

                // ── Section 2: Data ─────────────────────────────────────────
                Section::make('Detail Perubahan')
                    ->description('Data sebelum dan sesudah perubahan.')
                    ->icon('heroicon-o-code-bracket')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('old_data')
                            ->label('Data Lama')
                            ->formatStateUsing(fn ($state) => $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—')
                            ->extraAttributes(['style' => 'font-family: monospace; white-space: pre-wrap; font-size: 0.8rem;']),

                        TextEntry::make('new_data')
                            ->label('Data Baru')
                            ->formatStateUsing(fn ($state) => $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—')
                            ->extraAttributes(['style' => 'font-family: monospace; white-space: pre-wrap; font-size: 0.8rem;']),
                    ]),

            ]);
    }
}