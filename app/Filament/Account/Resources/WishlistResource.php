<?php

namespace App\Filament\Account\Resources;

use App\Filament\Account\Resources\WishlistResource\Pages;
use App\Models\BookUser;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Build;
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
}
