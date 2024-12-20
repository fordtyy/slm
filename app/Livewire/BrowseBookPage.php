<?php

namespace App\Livewire;

use App\Enums\BorrowStatus;
use App\Enums\PenaltyStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\BookUser;
use App\Models\Category;
use App\Models\Penalty;
use App\Models\Tag;
use App\Models\User;
use App\Services\BorrowService;
use Exception;
use Filament\Actions\Action as NativeAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Notification;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BrowseBookPage extends Component implements HasForms, HasActions
{
    use WithPagination;
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];
    public $borrow_temp = "";
    public $isPenalty = false;

    public function mount()
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.browse-book-page');
    }

    public function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    #[Computed]
    public function books()
    {

        $bookBorrows = BookBorrow::whereHas('borrow', fn($query) => $query->whereIn(
            'status',
            [
                BorrowStatus::PENDING,
                BorrowStatus::APPROVED,
                BorrowStatus::RELEASED,
                BorrowStatus::EXTENDED
            ]
        )
            ->where('user_id', Auth::id()))
            ->pluck('book_id');
        Auth::user()->course->name;


        $prompt = "Given the course " . Auth::user()->course->name .
            " suggest from the list of tags " .
            json_encode(Tag::pluck('name')->all()) .
            " that will help the student.The output should only be the PHP array of relevant tags based on the course title. Do not include any explanations, comments, markup or extra text in the response. Only provide the array as the final output. use double quote";

        $stringTags = Gemini::generateText($prompt); // Will return suggested tags based on course.

        $suggestedTags = json_decode($stringTags);

        $books = Book::when($this->data['title'], fn($query, $value) => $query->where('title', 'like', '%' . $value . '%'))
            ->when($this->data['category'], fn($query, $value) => $query->whereIn('category_id', $value))
            ->when($this->data['authors'], fn($query, $value) => $query->whereHas('authors', function ($query) use ($value) {
                $query->whereIn('author_id', $value);
            }))
            ->when($this->data['tags'], fn($query, $value) => $query->whereHas('tags', function ($query) use ($value) {
                $query->whereIn('tag_id', $value);
            }))
            ->when($suggestedTags, fn($query, $tags) => $query->whereHas('tags', function ($sub) use ($tags) {
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        $sub->orWhere('name', 'like', '%' . $tag . '%');
                    }
                }
            }))
            ->where('copies', '>', 0)
            ->whereNotIn('id', $bookBorrows)
            ->get();

        $preferredCategoryIds = $this->getUser()->categoryPrefs()->pluck('categories.id')->toArray();
        $preferredAuthorIds = $this->getUser()->authorPrefs()->pluck('authors.id')->toArray();

        // manual sort the books based on user's preferences
        $sortedBooks = $books->sortBy(function ($book) use ($preferredAuthorIds, $preferredCategoryIds, $suggestedTags) {
            $authorPriority = in_array($book->authors->first()->id, $preferredAuthorIds) ? 1 : 2; // 1 is book has an author that is preferred while 2 is not
            $categoryPriority = in_array($book->category_id, $preferredCategoryIds) ? 1 : 2; // 1 is book has category that is preferred while 2 is not
            $tagPriority = array_intersect($book->tags->pluck('name')->all(), $suggestedTags) ? 3 : 4;
            // how the book should be prioritized, the lower resuult is the more prioritized
            return $authorPriority * 10 + $categoryPriority + $tagPriority;
        });

        // Paginate manually
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = $sortedBooks->slice($offset, $perPage);

        $paginatedBooks = new LengthAwarePaginator(
            $itemsForCurrentPage,
            $sortedBooks->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $paginatedBooks;
    }

    public function addToWishListAction(): NativeAction
    {
        return NativeAction::make('addToWishList')
            ->outlined()
            ->hiddenLabel()
            ->icon("heroicon-o-heart")
            ->action(function (array $arguments) {
                Auth::user()->wishLists()->create(['book_id' => $arguments['book']]);
                Notification::make()
                    ->title('Added to wishlist!')
                    ->success()
                    ->send();
            });
    }

    public function addedToWishList(int $bookId): bool
    {
        return BookUser::where('user_id', Auth::id())
            ->where('book_id', $bookId)
            ->exists();
    }

    public function borrowAction(): NativeAction
    {
        return NativeAction::make('borrow')
            ->requiresConfirmation()
            ->modalHeading(fn($arguments) => 'Borrow ' . Book::find($arguments['book'])->title)
            ->modalDescription('By confirming, you agree that a 50 pesos fine wll be charged when you fail to return it
                                before or on due date of your request.')
            ->extraAttributes([
                'class' => 'flex-1 text-sm card-button-borrow',
            ])
            ->action(function (array $arguments) {

                if (Auth::user()->hasPenalties()) {
                    return Notification::make()
                        ->title('Action is Blocked!')
                        ->body('Your are not allowed to this request because you have existing penalties.')
                        ->warning()
                        ->send();
                }

                $record = Auth::user()->borrows()->create(['user_id' => Auth::id()]);
                BookBorrow::create(['book_id' => $arguments['book'], 'borrow_id' => $record->id]);
                BorrowService::updateStatus($record, BorrowStatus::PENDING->value);
                Notification::make()
                    ->title('New Borrow Created!')
                    ->success()
                    ->actions([
                        Action::make('view_student')
                            ->hidden(fn() => Auth::user()->type === 'admin')
                            ->url(fn() => route('filament.account.resources.borrows.view', $record))
                            ->label('View Request')
                    ])
                    ->sendToDatabase(Auth::user())
                    ->send();
                Notification::make()
                    ->title('New Borrow Created!')
                    ->success()
                    ->actions([
                        Action::make('view_admin')
                            ->label('View Request')
                            ->url(fn() => route('filament.admin.resources.borrows.view', $record)),
                    ])
                    ->sendToDatabase(User::where('type', 'admin')->first());
            });
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
                    ->live()
                    ->searchable()
                    ->placeholder('Search for category'),
                Select::make('authors')
                    ->native(false)
                    ->options(Author::pluck('name', 'id'))
                    ->multiple()
                    ->live()
                    ->searchable()
                    ->placeholder('Search for Author'),
                Select::make('tags')
                    ->native(false)
                    ->options(Tag::pluck('name', 'id'))
                    ->multiple()
                    ->live()
                    ->searchable()
                    ->placeholder('Search for Tag'),
            ])
            ->statePath('data')
            ->columns(4);
    }
}
