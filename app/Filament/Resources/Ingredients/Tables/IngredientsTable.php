<?php

namespace App\Filament\Resources\Ingredients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IngredientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Bahan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric(2)
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->stock <= 0                          => 'danger',
                        $record->stock <= ($record->min_stock ?? 0)  => 'warning',
                        default                                      => 'success',
                    })
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->unit),

                TextColumn::make('unit')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('min_stock')
                    ->label('Stok Minimum')
                    ->numeric(2)
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->unit)
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}