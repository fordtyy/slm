<?php

namespace App\Filament\Account\Resources\ExtensionResource\Pages;

use App\Enums\ExtensionStatus;
use App\Filament\Account\Resources\ExtensionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExtension extends ViewRecord
{
    protected static string $resource = ExtensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn($record) => $record?->status === ExtensionStatus::PENDING && !$record->payment()->exists()),
        ];
    }
}
