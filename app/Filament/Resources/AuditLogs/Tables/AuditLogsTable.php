<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CREATE' => 'success',
                        'UPDATE' => 'warning',
                        'DELETE' => 'danger',
                        default  => 'gray',
                    }),

                TextColumn::make('table_name')
                    ->label('Tabel')
                    ->badge()
                    ->color('info'),

                TextColumn::make('record_id')
                    ->label('ID Record')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->label('Filter Aksi')
                    ->options([
                        'CREATE' => '🟢 Create',
                        'UPDATE' => '🟡 Update',
                        'DELETE' => '🔴 Delete',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),
            ])
            ->toolbarActions([]);
    }
}