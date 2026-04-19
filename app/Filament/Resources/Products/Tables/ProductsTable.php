<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->getStateUsing(function ($record): ?string {
                        if (blank($record->image)) {
                            return null;
                        }

                        return trim($record->image);
                    })
                    ->height(48)
                    ->width(48)
                    ->square()
                    ->extraImgAttributes([
                        'style'   => 'object-fit: contain; background-color: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;',
                        'loading' => 'lazy',
                        'onerror' => "this.onerror=null;this.src='" . asset('images/no-image.png') . "';",
                    ])
                    ->defaultImageUrl(asset('images/no-image.png'))
                    ->visibleFrom('md'),

                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->description(fn ($record) => $record->category?->name),

                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('cost_price')
                    ->label('Harga Modal')
                    ->money('IDR')
                    ->sortable()
                    ->color('warning')
                    ->visibleFrom('lg'),

                TextColumn::make('margin')
                    ->label('Margin')
                    ->state(function ($record): string {
                        if (!$record->cost_price || $record->cost_price == 0) return '-';
                        $margin = (($record->selling_price - $record->cost_price) / $record->selling_price) * 100;
                        return number_format($margin, 1) . '%';
                    })
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->cost_price || $record->cost_price == 0) return 'gray';
                        $margin = (($record->selling_price - $record->cost_price) / $record->selling_price) * 100;
                        return match (true) {
                            $margin >= 50 => 'success',
                            $margin >= 25 => 'warning',
                            default       => 'danger',
                        };
                    })
                    ->visibleFrom('lg'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                SelectFilter::make('categories_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Status Produk')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
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
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->with(['category'])
                    ->select(
                        'id',
                        'name',
                        'image',
                        'categories_id',
                        'selling_price',
                        'cost_price',
                        'is_active',
                        'created_at'
                    );
            });
    }
}