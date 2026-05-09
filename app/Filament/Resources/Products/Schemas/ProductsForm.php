<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Section 1: Informasi Utama ─────────────────────────────
                Section::make('Informasi Utama')
                    ->description('Data dasar produk yang akan ditampilkan.')
                    ->icon('heroicon-o-tag')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->placeholder('Contoh: Kopi Arabika Gayo 250g')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Select::make('categories_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                    ]),

                // ── Section 2: Foto Produk ─────────────────────────────────
                Section::make('Foto Produk')
                    ->description('Gunakan rasio 1:1 untuk tampilan terbaik di tabel.')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('image')
                            ->label('')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->imageEditor()           // built-in crop & rotate
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('800')
                            ->maxSize(2048)           // 2 MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->deletable()
                            ->downloadable()
                            ->dehydrateStateUsing(fn ($state) => is_array($state) ? (array_values($state)[0] ?? null) : $state)
                            ->helperText('Format: JPG, PNG, WEBP · Maks 2MB · Rasio 1:1 direkomendasikan'),
                    ]),

                // ── Section 3: Harga & Margin ──────────────────────────────
                Section::make('Harga & Margin')
                    ->description('Margin dihitung otomatis dari selisih harga jual dan modal.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('selling_price')
                            ->label('Harga Jual')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->live(onBlur: true),    // trigger margin update

                        TextInput::make('cost_price')
                            ->label('Harga Modal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->live(onBlur: true),    // trigger margin update

                        // Margin preview — read-only, reactive
                        Placeholder::make('margin_preview')
                            ->label('Estimasi Margin')
                            ->columnSpanFull()
                            ->content(function (Get $get): string {
                                $sell = (float) ($get('selling_price') ?? 0);
                                $cost = (float) ($get('cost_price') ?? 0);

                                if ($sell <= 0) return '—';

                                $profit = $sell - $cost;
                                $margin = ($profit / $sell) * 100;

                                $emoji = match (true) {
                                    $margin >= 50 => '🟢',
                                    $margin >= 25 => '🟡',
                                    default       => '🔴',
                                };

                                $fmt = fn (float $n) => 'Rp ' . number_format($n, 0, ',', '.');

                                return "{$emoji} {$fmt($profit)} profit · margin {$margin}% "
                                    . "({$fmt($sell)} − {$fmt($cost)})";
                            }),
                    ]),

                // ── Section 4: Status ──────────────────────────────────────
                Section::make('Status Produk')
                    ->description('Produk nonaktif tidak akan muncul di daftar penjualan.')
                    ->icon('heroicon-o-check-badge')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->helperText('Aktifkan agar produk bisa dijual.')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }
}