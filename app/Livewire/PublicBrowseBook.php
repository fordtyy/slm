<?php

namespace App\Livewire;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.public-browse-book')]
class PublicBrowseBook extends Component implements HasForms, HasActions
{

    use WithPagination;
    use InteractsWithForms;
    use InteractsWithActions;


    protected static string $view = 'livewire.public-browse-book';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill();

        // if (Filament::auth()->check()) {
        //     if (Filament::auth() && Filament::auth()->user()->type === 'student') {
        //         return redirect()->route('filament.account.pages.account-dashboard');
        //     } else {
        //         return redirect()->route('filament.admin.pages.dashboard');
        //     }
        // }
    }

    #[Computed]
    public function getFilteredBooksProperty()
    {
        return Book::when($this->data['title'], fn($query, $value) => $query->where('title', 'like', '%' . $value . '%'))
            ->when($this->data['category'], fn($query, $value) => $query->whereIn('category_id', $value))
            ->when($this->data['authors'], fn($query, $value) => $query->whereHas('authors', function ($query) use ($value) {
                $query->whereIn('author_id', $value);
            }))
            ->when($this->data['tags'], fn($query, $value) => $query->whereHas('tags', function ($query) use ($value) {
                $query->whereIn('tag_id', $value);
            }))
            ->where('copies', '>', 0)
            ->paginate(12);
    }

    public function borrowAction(): Action
    {
        return Action::make('borrow')
            ->extraAttributes([
                'class' => 'flex-1 text-sm card-button-borrow',
            ])
            ->action(fn() => redirect()->route('filament.auth.auth.login'));
    }

    public function addedToWishList(int $bookId): bool
    {
        return true;
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

    public function render()
    {
        return view(self::$view);
    }
}
