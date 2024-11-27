<?php

namespace App\Filament\Admin\Resources\BorrowResource\Pages;

use App\Filament\Admin\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Request')
                ->modalHeading('Add New Borrow Request'),
        ];
    }
}
