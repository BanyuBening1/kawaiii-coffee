<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'ingredients';
    protected static ?string $inverseRelationship = 'products';
    protected static ?string $title = 'Resep & Bahan Baku';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('quantity')
                    ->label('Jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Daftar Bahan Baku')
            ->description('Bahan baku yang digunakan dalam resep produk ini.')
            ->emptyStateHeading('Belum ada bahan baku')
            ->emptyStateDescription('Tambahkan bahan baku yang dibutuhkan untuk membuat produk ini.')
            ->emptyStateIcon('heroicon-o-beaker')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Bahan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('pivot.quantity')
                    ->label('Jumlah Dipakai')
                    ->numeric(2)
                    ->getStateUsing(fn ($record) => $record->pivot?->quantity ?? '-')
                    ->badge()
                    ->color('info'),

                TextColumn::make('unit')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('stock')
                    ->label('Stok Tersedia')
                    ->numeric(2)
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->stock <= 0              => 'danger',
                        $record->stock <= ($record->min_stock ?? 0) => 'warning',
                        default                          => 'success',
                    })
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->unit),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Bahan')
                    ->modalHeading('Tambah Bahan ke Resep')
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Pilih Bahan Baku')
                            ->searchable()
                            ->preload(),

                        TextInput::make('quantity')
                            ->label('Jumlah yang Dibutuhkan')
                            ->placeholder('Contoh: 250')
                            ->helperText('Jumlah bahan per 1 produk yang dibuat.')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1),
                    ]),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Bahan dari Resep')
                    ->modalDescription('Bahan ini akan dihapus dari resep produk.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ])
            ->bulkActions([
                DetachBulkAction::make()
                    ->label('Hapus Terpilih'),
            ]);
    }
}