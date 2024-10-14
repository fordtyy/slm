<?php

namespace App\Filament\Account\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class CategoryPreferred extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static string $view = 'filament.account.pages.category-preferred';

  protected static ?string $routePath = 'category-preferred';

  protected ?string $heading = 'Select Preferred Categories';

  public static function shouldRegisterNavigation(): bool
  {
    return false;
  }
}
