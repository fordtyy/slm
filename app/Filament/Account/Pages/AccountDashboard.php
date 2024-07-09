<?php

namespace App\Filament\Account\Pages;

use App\Filament\Account\Widgets\DatesOverview;
use App\Filament\Account\Widgets\PendingBorrowRequests;
use Filament\Pages\Dashboard;

class AccountDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.account.pages.account-dashboard';

    protected static string $routePath = 'dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            DatesOverview::class,
            PendingBorrowRequests::class,
        ];
    }

    public function getColumns(): int
    {
        return 3;
    }


}
