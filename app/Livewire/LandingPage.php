<?php

namespace App\Livewire;

use App\Models\Book;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\Alignment;
use Livewire\Component;

class LandingPage extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    public $books = [];

    public function mount()
    {
        $this->books = Book::latest()->take(10)->get();
    }

    public function render()
    {
        return view('livewire.landing-page');
    }

    public function booksInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([
                'books' => $this->books
            ])
            ->schema([
                RepeatableEntry::make('books')
                    ->hiddenLabel()
                    ->schema([
                        Section::make()
                            ->id('books')
                            ->schema([
                                ImageEntry::make('cover')
                                    ->alignCenter()
                                    ->hiddenLabel(),
                            ])
                            ->footerActions([
                                Action::make('borrow')
                                    ->label('Borrow Now')
                                    ->url(fn (Book $record) => route('filament.account.pages.borrow-book', ['book_id' => $record->id])),
                                Action::make('favorite')
                                    ->icon('heroicon-o-heart')
                                    ->iconButton(),
                            ])
                            ->footerActionsAlignment(Alignment::Between)
                    ])
                    ->contained(false)
                    ->grid(6)
                    ->alignCenter()
            ]);
    }
}
