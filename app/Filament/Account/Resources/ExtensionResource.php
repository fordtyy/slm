<?php

namespace App\Filament\Account\Resources;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Filament\Account\Resources\ExtensionResource\Pages;
use App\Filament\Account\Resources\ExtensionResource\RelationManagers;
use App\Models\Extension;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static ?string $navigationGroup = 'Request';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('borrow_id')
                    ->label('Borrow Request')
                    ->relationship('borrow', 'code', fn($query) => $query->where('status', BorrowStatus::RELEASED)->where('user_id', Auth::id())->doesntHave('extension'))
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->visible(fn(string $operation) => $operation == 'create')
                    ->required(),
                Forms\Components\ToggleButtons::make('number_of_days')
                    ->options(ExtensionDays::class)
                    ->inline()
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_days')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Request At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('borrow.code')
                    ->extraAttributes([
                        'class' => 'hover:underline'
                    ])
                    ->action(
                        Tables\Actions\Action::make('borrow_request_details')
                            ->slideOver()
                            ->infolist([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('borrow.code')
                                        ->label('Code'),
                                    Infolists\Components\TextEntry::make('borrow.status')
                                        ->label('Status')
                                        ->badge(),
                                    Infolists\Components\TextEntry::make('borrow.due_date')
                                        ->label('Due Date'),
                                ])->columns(3),
                                Infolists\Components\Section::make('Books')
                                    ->schema([
                                        Infolists\Components\RepeatableEntry::make('borrow.books')
                                            ->hiddenLabel()
                                            ->schema([
                                                Infolists\Components\Group::make([
                                                    Infolists\Components\TextEntry::make('title'),
                                                    Infolists\Components\TextEntry::make('authors.name')
                                                        ->badge(),
                                                ])

                                            ])
                                    ])

                            ])
                            ->modalCancelAction(false)
                            ->modalSubmitActionLabel('Close')
                            ->modalFooterActionsAlignment(Alignment::Right)
                    )
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->status == ExtensionStatus::PENDING),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->status == ExtensionStatus::PENDING),
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
            'index' => Pages\ListExtensions::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
      return !in_array(Request::route()->getName(), ['filament.account.pages.category-preferred', 'filament.account.pages.author-preferred']);
    }
}
