<?php

namespace App\Livewire;

use App\Enums\BorrowStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\BookUser;
use App\Models\Borrow;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use phpDocumentor\Reflection\Types\Boolean;

class BrowseBookPage extends Component implements HasForms, HasActions
{
  use WithPagination;
  use InteractsWithForms;
  use InteractsWithActions;

  public ?array $data = [];
  public $borrow_temp = "";

  public function mount()
  {
    $this->form->fill();
  }

  public function render()
  {
    return view('livewire.browse-book-page');
  }

  #[Computed]
  public function books()
  {
    $bookBorrows = BookBorrow::whereHas('borrow', fn($query) => $query->whereIn('status', [BorrowStatus::PENDING, BorrowStatus::APPROVED])
      ->where('user_id', Auth::id()))
      ->pluck('book_id');


    return Book::when($this->data['title'], fn($query, $value) => $query->where('title', 'like', '%' . $value . '%'))
      ->when($this->data['category'], fn($query, $value) => $query->whereIn('category_id', $value))
      ->when($this->data['authors'], fn($query, $value) => $query->whereHas('authors', function ($query) use ($value) {
        $query->whereIn('author_id', $value);
      }))
      ->when($this->data['tags'], fn($query, $value) => $query->whereHas('tags', function ($query) use ($value) {
        $query->whereIn('tag_id', $value);
      }))
      ->whereNotIn('id', $bookBorrows)
      ->paginate(9);
  }

  public function addToWishList(Book $book)
  {
    Auth::user()->wishLists()->create(['book_id' => $book->id]);
    Notification::make()
      ->title('Added to wishlist!')
      ->success()
      ->send();
  }

  public function addedToWishList(Book $book): bool
  {
    return BookUser::where('user_id', Auth::id())
      ->where('book_id', $book->id)
      ->exists();
  }

  public function confirmBorrow()
  {
    $record = Auth::user()->borrows()->create(['user_id' => Auth::id()]);
    BookBorrow::create(['book_id' => $this->borrow_temp, 'borrow_id' => $record->id]);
    $this->closeModal();
    Notification::make()
      ->title('Borrow Successfully!')
      ->success()
      ->send();
  }

  public function borrowBook(Book $book)
  {
    $this->borrow_temp = $book->id;
    $this->dispatch('open-modal', id: 'borrow-modal');
  }

  public function closeModal()
  {
    $this->dispatch('close-modal', id: 'borrow-modal');
  }

  #[On('close-modal')]
  public function afterModalClose(): void
  {
    $this->borrow_temp = "";
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        TextInput::make('title')
          ->live()
          ->autocapitalize('words')
          ->placeholder('Search for Title')
          ->minLength(2)
          ->maxLength(100),
        Select::make('category')
          ->native(false)
          ->options(Category::pluck('name', 'id'))
          ->multiple()
          ->optionsLimit(10)
          ->live()
          ->searchable()
          ->placeholder('Search for category'),
        Select::make('authors')
          ->native(false)
          ->options(Author::pluck('name', 'id'))
          ->multiple()
          ->optionsLimit(10)
          ->live()
          ->searchable()
          ->placeholder('Search for Author'),
        Select::make('tags')
          ->native(false)
          ->options(Tag::pluck('name', 'id'))
          ->multiple()
          ->optionsLimit(10)
          ->live()
          ->searchable()
          ->placeholder('Search for Tag'),
      ])
      ->statePath('data')
      ->columns(4);
  }
}
