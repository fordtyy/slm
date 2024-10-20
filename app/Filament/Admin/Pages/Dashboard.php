<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function getColumns(): int | string | array
    {
        return 12;
    }
}
