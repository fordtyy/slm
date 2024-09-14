<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Borrow;
use Filament\Facades\Filament;
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
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LandingPage extends Component implements HasForms, HasInfolists
{
  use InteractsWithInfolists;
  use InteractsWithForms;

  public $books = [];
  public $youMayLike = [];

  public function mount()
  {
    if (Filament::auth()->check()) {
      if (Filament::auth() && Filament::auth()->user()->type === 'student') {
        return redirect()->route('filament.account.pages.account-dashboard');
      } else {
        return redirect()->route('filament.admin.pages.dashboard');
      }
    }

    $bookTemp = BookBorrow::select('book_id')
      ->whereIn('book_id', function ($query) {
        $query->select('book_id')
          ->from('book_borrow')
          ->groupBy('book_id')
          ->havingRaw('COUNT(*) > 0');
      })
      ->groupBy('book_id')
      ->orderByRaw('(SELECT COUNT(*) FROM book_borrow AS t2 WHERE t2.book_id = book_borrow.book_id) DESC')
      ->take(10)
      ->get();

    $this->youMayLike = Book::inRandomOrder()->limit(10)->get();
    $this->books = Book::whereIn('id', $bookTemp)->get();
  }

  public function borrowBook(Book $book)
  {
    redirect()->route('filament.auth.auth.login');
  }

  public function addToWishList(Book $book){}

  public function addedToWishList(Book $book): bool
  {
    return true;
  }

  public function render()
  {
    return view('livewire.landing-page');
  }
}
