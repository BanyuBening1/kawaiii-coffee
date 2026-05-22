<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Models\Transactions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

// IMPORT COMPONENT STANDAR FILAMENT V4
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_code')
                    ->label('Kode Transaksi')
                    ->searchable()
                    ->copyable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-m-receipt-percent'),

                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->icon('heroicon-m-calendar-days'),

                TextColumn::make('cashier.name')
                    ->label('Kasir')
                    ->searchable()
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->badge()
                    ->icon('heroicon-m-credit-card'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'paid' => 'Lunas',
                        'pending' => 'Pending',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->toggleable(),

                TextColumn::make('change_amount')
                    ->label('Kembalian')
                    ->money('IDR')
                    ->toggleable(),
            ])
            ->defaultSort('transaction_date', 'desc')

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'paid' => 'Lunas',
                        'pending' => 'Pending',
                        'cancelled' => 'Dibatalkan',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Tunai',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                    ]),
            ])

            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Transaksi')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn(Transactions $record) => view(
                        'filament.transactions.detail',
                        ['transaction' => $record]
                    )),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}