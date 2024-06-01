<?php

namespace App\Filament\Admin\Resources\BorrowResource\Pages;

use App\Filament\Admin\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBorrow extends ViewRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
