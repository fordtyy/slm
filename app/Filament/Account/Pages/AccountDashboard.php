<?php

namespace App\Filament\Account\Pages;

use App\Filament\Account\Widgets\DatesOverview;
use App\Filament\Account\Widgets\PendingBorrowRequests;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Request;

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

  public static function shouldRegisterNavigation(): bool
  {
    return !in_array(Request::route()->getName(), ['filament.account.pages.category-preferred', 'filament.account.pages.author-preferred']);
  }
}
