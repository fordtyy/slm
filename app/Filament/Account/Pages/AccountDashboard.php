<?php

namespace App\Filament\Account\Pages;

use Filament\Pages\Dashboard;
use Filament\Pages\Page;

class AccountDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.account.pages.account-dashboard';

    protected static string $routePath = 'dashboard';
}
