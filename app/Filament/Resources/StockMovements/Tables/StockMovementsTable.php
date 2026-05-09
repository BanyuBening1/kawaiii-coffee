<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ingredient.name')
                    ->label('Bahan Baku')
                    ->searchable()
                    ->weight('medium'),

                TextColumn::make('user.name')
                    ->label('Dicatat Oleh')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'IN'  => 'success',
                        'OUT' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (strtoupper($state)) {
                        'IN'  => '📥 Masuk',
                        'OUT' => '📤 Keluar',
                        default => $state,
                    }),

                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->sortable(),

                TextColumn::make('reference')
                    ->label('Referensi')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc') // ← terbaru di atas
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}