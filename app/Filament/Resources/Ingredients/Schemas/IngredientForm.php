<?php

namespace App\Filament\Resources\Ingredients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IngredientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Section 1: Informasi Bahan Baku ────────────────────────
                Section::make('Informasi Bahan Baku')
                    ->description('Data dasar bahan baku untuk kebutuhan stok.')
                    ->icon('heroicon-o-beaker')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Bahan')
                            ->placeholder('Contoh: Biji Kopi Arabika')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('stock')
                            ->label('Jumlah Stok')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
                            ->visibleOn('create'), // ← sembunyikan saat edit

                        Select::make('unit')
                            ->label('Satuan')
                            ->options([
                                'gr'  => 'Gram (gr)',
                                'ml'  => 'Mililiter (ml)',
                                'pcs' => 'Pcs',
                            ])
                            ->required(),

                        TextInput::make('min_stock')
                            ->label('Stok Minimum')
                            ->placeholder('Contoh: 100')
                            ->helperText('Sistem akan memperingatkan jika stok di bawah angka ini.')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                    ]),
            ]);
    }
}