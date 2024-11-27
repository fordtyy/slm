<?php

namespace App\Filament\Admin\Resources\BorrowResource\Pages;

use App\Filament\Admin\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrow extends EditRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
