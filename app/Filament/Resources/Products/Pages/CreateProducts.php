<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductsResource;
use App\Models\Ingredients;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function afterCreate(): void
    {
        $recipe = $this->data['recipe'] ?? [];

        if (empty($recipe)) {
            Notification::make()
                ->title('Tambahkan bahan baku!')
                ->body('Produk harus memiliki minimal 1 bahan baku.')
                ->warning()
                ->persistent()
                ->send();
            return;
        }

        // Simpan ingredient ke pivot table
        $ingredients = collect($recipe)->mapWithKeys(fn ($item) => [
            $item['ingredient_id'] => ['quantity' => $item['quantity']],
        ])->toArray();

        $this->getRecord()->ingredients()->sync($ingredients);

        Notification::make()
            ->title('Produk berhasil dibuat!')
            ->body('Bahan baku telah disimpan.')
            ->success()
            ->send();
    }
}