<?php

namespace App\Filament\Account\Resources\BorrowResource\Pages;

use App\Enums\PenaltyStatus;
use App\Filament\Account\Resources\BorrowResource;
use App\Models\Penalty;
use Exception;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected static string $view = 'filament-panels::resources.pages.list-records';

    public function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }
}
