<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\TextSize;

use Filament\Schemas\Components\Section;

use Filament\Schemas\Schema;

class TransactionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 🔹 INFORMASI TRANSAKSI
                Section::make('Informasi Transaksi')
                    ->icon('heroicon-o-receipt-percent')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('transaction_code')
                            ->label('Kode Transaksi')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->copyable()
                            ->copyMessage('Kode disalin!'),

                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-m-calendar-days'),

                        TextEntry::make('cashier.name')
                            ->label('Kasir')
                            ->icon('heroicon-m-user-circle'),

                        TextEntry::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->badge()
                            ->icon('heroicon-m-credit-card'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid'      => 'success',
                                'pending'   => 'warning',
                                'cancelled' => 'danger',
                                default     => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'paid'      => 'Lunas',
                                'pending'   => 'Pending',
                                'cancelled' => 'Dibatalkan',
                                default     => $state,
                            }),
                    ]),

                // 🔹 RINGKASAN PEMBAYARAN
                Section::make('Ringkasan Pembayaran')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('total')
                            ->money('IDR')
                            ->label('Total Belanja')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->color('success'),

                        TextEntry::make('paid_amount')
                            ->money('IDR')
                            ->label('Jumlah Dibayar'),

                        TextEntry::make('change_amount')
                            ->money('IDR')
                            ->label('Kembalian')
                            ->color('warning'),
                    ]),

                // 🔹 DETAIL PRODUK
                Section::make('Detail Produk')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        RepeatableEntry::make('details')
                            ->label('')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Nama Produk')
                                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                                TextEntry::make('quantity')
                                    ->label('Qty')
                                    ->suffix(' pcs'),
                                    // ->alignCenter(),

                                TextEntry::make('unit_price')
                                    ->money('IDR')
                                    ->label('Harga Satuan'),

                                TextEntry::make('subtotal')
                                    ->money('IDR')
                                    ->label('Subtotal')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                    ->color('success'),
                            ])
                            ->columns(4),
                    ]),

                // 🔹 TIMESTAMP
                Section::make('Waktu Pencatatan')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime('d M Y, H:i')
                            ->label('Dibuat pada'),

                        TextEntry::make('updated_at')
                            ->dateTime('d M Y, H:i')
                            ->label('Diupdate pada'),
                    ]),

            ]);
    }
}