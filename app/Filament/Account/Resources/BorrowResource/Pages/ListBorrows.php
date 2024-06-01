<?php

namespace App\Filament\Account\Resources\BorrowResource\Pages;

use App\Filament\Account\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
