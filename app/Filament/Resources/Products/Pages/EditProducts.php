<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductsResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProducts extends EditRecord
{
    protected static string $resource = ProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    protected function beforeSave(): void
    {
        $ingredientCount = $this->record->ingredients()->count();

        if ($ingredientCount === 0) {
            Notification::make()
                ->title('Gagal menyimpan!')
                ->body('Produk harus memiliki minimal 1 bahan baku.')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}