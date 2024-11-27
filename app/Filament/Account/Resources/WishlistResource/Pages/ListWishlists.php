<?php

namespace App\Filament\Account\Resources\WishlistResource\Pages;

use App\Filament\Account\Resources\WishlistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWishlists extends ListRecords
{
    protected static string $resource = WishlistResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
