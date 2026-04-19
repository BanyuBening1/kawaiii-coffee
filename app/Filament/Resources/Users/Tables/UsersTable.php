<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom utama: nama + email dalam satu sel (lebih compact)
                TextColumn::make('name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->description(fn ($record) => $record->email),

                TextColumn::make('role.roles_name')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Admin'   => 'danger',
                        'Kasir'   => 'success',
                        default   => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->date('d M Y')       // format ringkas: "12 Jan 2025"
                    ->sortable()
                    ->color('gray'),
            ])

            ->defaultSort('created_at', 'desc')

            // Tidak perlu pagination untuk 3–6 user
            ->paginated(false)

            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->relationship('role', 'roles_name')
                    ->preload(),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading('Belum ada pengguna')
            ->emptyStateDescription('Tambahkan pengguna pertama untuk mulai.')

            ->modifyQueryUsing(fn ($query) => $query
                ->with(['role'])
                ->select('id', 'name', 'email', 'role_id', 'created_at')
            );
    }
}