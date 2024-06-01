<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BorrowResource\Pages;
use App\Filament\Admin\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('start_date')
                    ->native(false),
                Forms\Components\DateTimePicker::make('due_date')
                    ->native(false),
                Forms\Components\Select::make('books')
                    ->relationship(name: 'books', titleAttribute: 'title')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Borrower'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Submitted At'),
                Tables\Columns\TextColumn::make('books.title')
                    ->badge()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            'view' => Pages\ViewBorrow::route('/{record}'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
