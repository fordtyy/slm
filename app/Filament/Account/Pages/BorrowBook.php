<?php

namespace App\Filament\Account\Pages;

use App\Filament\Admin\Resources\BorrowResource;
use App\Models\Book;
use App\Models\Borrow;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Request;
use Livewire\Component;

class BorrowBook extends Page implements HasForms
{

    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.account.pages.borrow-book';

    public $selectedBook = null;

    public function mount(): void
    {
        $this->selectedBook = Book::find(request()->book_id);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(Borrow::class)
            ->schema([
                Forms\Components\Select::make('books')
                    ->relationship(name: 'books', titleAttribute: 'title')


            ]);
    }
}
