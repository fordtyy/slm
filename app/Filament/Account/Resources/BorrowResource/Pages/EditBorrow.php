<?php

namespace App\Filament\Account\Resources\BorrowResource\Pages;

use App\Filament\Account\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
