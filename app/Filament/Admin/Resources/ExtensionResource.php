<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Filament\Admin\Resources\ExtensionResource\Pages;
use App\Filament\Admin\Resources\ExtensionResource\RelationManagers;
use App\Models\Extension;
use App\Services\ExtensionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;

use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static ?string $navigationGroup = 'Requests';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('borrow_id')
                    ->label('Borrow Request')
                    ->relationship('borrow', 'code')
                    ->native(false)
                    ->searchable()
                    ->preload()
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_of_days')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->date()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status == ExtensionStatus::PENDING)
                    ->action(function (Extension $record) {
                        ExtensionService::updateStatus($record, ExtensionStatus::APPROVED->value);
                    }),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->visible(fn($record) => $record->status == ExtensionStatus::PENDING)
                    ->requiresConfirmation()
                    ->action(function (Extension $record) {
                        ExtensionService::updateStatus($record, ExtensionStatus::REJECTED->value);
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Borrower')
                    ->relationship('borrow.user', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
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
            'index' => Pages\ListExtensions::route('/'),
            'create' => Pages\CreateExtension::route('/create'),
            'edit' => Pages\EditExtension::route('/{record}/edit'),
        ];
    }
}
