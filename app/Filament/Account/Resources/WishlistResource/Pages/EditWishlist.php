<?php

namespace App\Filament\Account\Resources\WishlistResource\Pages;

use App\Filament\Account\Resources\WishlistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWishlist extends EditRecord
{
    protected static string $resource = WishlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
