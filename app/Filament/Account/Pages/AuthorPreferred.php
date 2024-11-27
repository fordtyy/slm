<?php

namespace App\Filament\Account\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Request;

class AuthorPreferred extends Page
{
  protected static ?string $navigationIcon = 'heroicon-o-document-text';

  protected static string $view = 'filament.account.pages.author-preferred';

  protected ?string $heading = 'Select Preferred Authors';

  public static function shouldRegisterNavigation(): bool
  {
    return false;
  }
}
