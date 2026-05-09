<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Section 1: Detail Pergerakan ────────────────────────────
                Section::make('Detail Pergerakan Stok')
                    ->description('Catat bahan baku yang masuk atau keluar.')
                    ->icon('heroicon-o-arrow-path')
                    ->columns(2)
                    ->schema([
                        Select::make('ingredient_id')
                            ->label('Bahan Baku')
                            ->relationship('ingredient', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Jenis Pergerakan')
                            ->options([
                                'in'  => '📥 Masuk',
                                'out' => '📤 Keluar',
                            ])
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TextInput::make('reference')
                            ->label('Referensi')
                            ->placeholder('Contoh: PO-2024-001')
                            ->helperText('Nomor dokumen atau catatan terkait (opsional).'),
                    ]),

                // ── Section 2: Pencatat ─────────────────────────────────────
                Section::make('Dicatat Oleh')
                    ->description('Pengguna yang melakukan pencatatan stok.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Select::make('user_id')
                            ->label('Nama Pengguna')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                // ── Section 3: Keterangan ───────────────────────────────────
                Section::make('Keterangan')
                    ->description('Tambahkan catatan tambahan jika diperlukan.')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Textarea::make('description')
                            ->label('Catatan')
                            ->placeholder('Contoh: Restock mingguan dari supplier utama')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}