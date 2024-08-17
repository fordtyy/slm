<?php

namespace App\Filament\Account\Pages;

use App\Models\Book;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Livewire\WithPagination;

class BrowseBooks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.account.pages.browse-books';

    protected static ?string $routePath = 'browse';

    protected static ?string $navigationLabel = 'Browse';
}
