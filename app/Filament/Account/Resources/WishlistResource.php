<?php

namespace App\Filament\Account\Resources;

use App\Filament\Account\Resources\WishlistResource\Pages;
use App\Models\BookBorrow;
use App\Models\BookUser;
use App\Models\User;
use Exception;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotifActions;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class WishlistResource extends Resource
{
    protected static ?string $model = BookUser::class;

    protected static ?string $label = "Wishlists";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())
        ->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('book.cover'),
                TextColumn::make('book.title')
                  ->label('Book Title'),
                TextColumn::make('book.copies')
                  ->label('Copies'),
                TextColumn::make('book.authors.name')
                  ->badge()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                BulkAction::make('Borrow Books')
                ->action(function (Collection $records, array $data) {
                    try {
                        $borrowRecord = Auth::user()->borrows()->create(['user_id' => Auth::id()]);

                        $wishlistIds = $records->pluck('id')->toArray();
                        $data = self::getBookIdsWithBookBorrowId($records, $borrowRecord);

                        BookBorrow::insert($data);
                        BookUser::whereIn('id', $wishlistIds)->delete();

                        Notification::make()
                            ->title('New Borrow Created!')
                            ->success()
                            ->actions([
                                NotifActions::make('view_student')
                                    ->hidden(fn() => Auth::user()->type === 'admin')
                                    ->url(fn() => route('filament.account.resources.borrows.view', $borrowRecord))
                                    ->label('View Request')
                            ])
                            ->sendToDatabase(Auth::user())
                            ->send();
                        Notification::make()
                            ->title('New Borrow Created!')
                            ->success()
                            ->actions([
                                NotifActions::make('view_admin')
                                    ->label('View Request')
                                    ->url(fn() => route('filament.admin.resources.borrows.view', $borrowRecord)),
                            ])
                            ->sendToDatabase(User::where('type', 'admin')->first());
                        header("Refresh:0");
                    } catch (Exception $e) {
                        echo 'Message: ' .$e->getMessage();
                    }
                })
                ->requiresConfirmation(),
            ])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWishlists::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
      return !in_array(Request::route()->getName(), ['filament.account.pages.category-preferred', 'filament.account.pages.author-preferred']);
    }

    public static function getBookIdsWithBookBorrowId(Collection $records, $borrowRecord): array
    {
        $data = $records->map(function ($item) {
            return [
                'book_id' => $item->book_id
            ];
        })->toArray();

        foreach ($data as $index => $item) {
            $data[$index]['borrow_id'] = $borrowRecord->id;
        }

        return $data;
    }
}
