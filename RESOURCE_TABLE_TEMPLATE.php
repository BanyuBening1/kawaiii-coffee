<?php

// 📌 TEMPLATE: Optimized Filament Resource Table
// Gunakan template ini sebagai patokan untuk resource lainnya

namespace App\Filament\Resources\YourResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class YourResourceTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ✅ LANGKAH 1: Define columns
            ->columns([
                TextColumn::make('id')
                    ->badge()
                    ->numeric(),
                
                TextColumn::make('name')
                    ->searchable(),
                
                // ✅ LANGKAH 2: Relationship column dengan proper naming
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable('categories.name'),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            // ✅ LANGKAH 3: Set default sorting
            ->defaultSort('id', 'desc')
            
            // ✅ LANGKAH 4: Configure pagination options
            ->paginated([10, 25, 50])
            
            // ✅ LANGKAH 5: Add filters
            ->filters([
                // SelectFilter::make('category')
                //     ->relationship('category', 'name')
            ])
            
            // ✅ LANGKAH 6: Define actions
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            
            // ✅ LANGKAH 7: CRITICAL - Eager load all relationships
            // Ini adalah kunci utama performa!
            ->modifyQueryUsing(function ($query) {
                return $query
                    ->with(['category'])           // Single relationship
                    // ->with(['author', 'comments'])  // Multiple relationships
                    // ->with(['items.product'])       // Nested relationships
                    ->select(
                        'id',
                        'name',
                        'categories_id',
                        'created_at',
                        // List semua kolom yang digunakan di table
                        // Jangan gunakan select(*) - spesifik kolom yang perlu
                    );
            });
    }
}

/*
 * ═══════════════════════════════════════════════════════════════════
 * 📋 CHECKLIST untuk setiap Resource Table:
 * ═══════════════════════════════════════════════════════════════════
 * 
 * ✅ Column naming conventions
 * ✅ Relationship columns dengan dot notation (category.name)
 * ✅ Default sort di set
 * ✅ Pagination options di set (bukan default)
 * ✅ Unnecessary columns di hide (toggleable)
 * ✅ Responsive visibility (visibleFrom)
 * ✅ modifyQueryUsing dengan .with() untuk eager loading
 * ✅ Select specific columns jika tabel besar
 * ✅ Join table jika search pada relationship
 * ✅ Index pada database untuk foreign keys
 * 
 * ═══════════════════════════════════════════════════════════════════
 * 🔍 COMMON MISTAKES:
 * ═══════════════════════════════════════════════════════════════════
 * 
 * ❌ TextColumn::make('categories_id') 
 *    → Menampilkan ID bukan nama
 *    ✅ TextColumn::make('category.name')
 * 
 * ❌ Tidak ada modifyQueryUsing dengan .with()
 *    → Terjadi N+1 Query
 *    ✅ ->modifyQueryUsing(fn($q) => $q->with(['category']))
 * 
 * ❌ ->paginated([100]) atau default
 *    → Loading slow, render heavy
 *    ✅ ->paginated([10, 25, 50])
 * 
 * ❌ Semua kolom selalu visible
 *    → Terlalu banyak data di render
 *    ✅ ->toggleable(isToggledHiddenByDefault: true)
 *    ✅ ->visibleFrom('md')
 * 
 * ❌ Relationship column tanpa searchable('table.column')
 *    → Search tidak bekerja atau error
 *    ✅ ->searchable('categories.name')
 */
