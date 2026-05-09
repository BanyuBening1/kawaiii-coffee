<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\ViewTransactions;
use App\Filament\Resources\Transactions\Schemas\TransactionsForm;
use App\Filament\Resources\Transactions\Schemas\TransactionsInfolist;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transactions;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Laporan';
    }

    protected static ?string $recordTitleAttribute = 'transaction_code';

    public static function form(Schema $schema): Schema
    {
        return TransactionsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransactionsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'view' => ViewTransactions::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;   
    }
}
