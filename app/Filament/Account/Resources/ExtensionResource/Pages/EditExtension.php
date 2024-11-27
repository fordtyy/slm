<?php

namespace App\Filament\Account\Resources\ExtensionResource\Pages;

use App\Filament\Account\Resources\ExtensionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtension extends EditRecord
{
    protected static string $resource = ExtensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
