<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Status;
use App\Filament\Admin\Resources\BookResource\Pages;
use App\Filament\Admin\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\FileUpload::make('cover')
                        ->required()
                        ->columnspan(1),
                    Forms\Components\TextInput::make('isbn')
                        ->required()
                        ->unique(ignoreRecord: true),
                ])
                    ->columns(2),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->columnspan(2),
                ])
                    ->columns(2),

                Forms\Components\Group::make([

                    Forms\Components\TextInput::make('edition')
                        ->required(),
                    Forms\Components\TextInput::make('label')
                        ->required(),
                    Forms\Components\DatePicker::make('year')
                        ->required()
                        ->native(false)
                        ->displayFormat('Y'),
                    Forms\Components\Select::make('category_id')
                        ->relationship(name: 'category', titleAttribute: 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                        ])
                        ->searchable()
                        ->native(false)
                        ->required()
                        ->unique(ignoreRecord: true),
                ])
                    ->columns(2),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('volume')
                        ->required(),
                    Forms\Components\TextInput::make('copies')
                        ->numeric()
                        ->required(),

                    Forms\Components\Select::make('author')
                        ->relationship(name: 'authors', titleAttribute: 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                        ])
                        ->multiple()
                        ->unique(ignoreRecord: true)
                        ->searchable()
                        ->native(false)
                        ->required(),
                    Forms\Components\Select::make('tag')
                        ->relationship(name: 'tag', titleAttribute: 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                        ])
                        ->searchable()
                        ->multiple()
                        ->native(false)
                        ->required()
                        ->unique(ignoreRecord: true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover'),
                Tables\Columns\TextColumn::make('isbn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('edition')
                    ->searchable(),
                Tables\Columns\TextColumn::make('label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('volume')
                    ->searchable(),
                Tables\Columns\TextColumn::make('authors.name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tag.name')
                    // ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\ImageEntry::make('cover')
                        ->height(400)
                        ->square()
                ]),

                Infolists\Components\Group::make([

                    Infolists\Components\Group::make([
                        Infolists\Components\TextEntry::make('isbn')
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ])
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                        Infolists\Components\TextEntry::make('title')
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ])
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                    ])
                        ->columns(2),
                    Infolists\Components\Group::make([
                        Infolists\Components\TextEntry::make('label')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                        Infolists\Components\TextEntry::make('edition')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                        Infolists\Components\TextEntry::make('year')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                        Infolists\Components\TextEntry::make('volume')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                    ])
                        ->columns(4),

                    Infolists\Components\Group::make([
                        Infolists\Components\TextEntry::make('category.name')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                        Infolists\Components\TextEntry::make('authors.name')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                        Infolists\Components\TextEntry::make('tag.name')
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Medium),
                    ])
                        ->columns(3),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'view' => Pages\ViewBook::route('/{record}'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
