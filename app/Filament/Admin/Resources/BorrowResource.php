<?php

namespace App\Filament\Admin\Resources;

use App\Enums\BorrowStatus;
use App\Filament\Admin\Resources\BorrowResource\Pages;
use App\Filament\Admin\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use App\Services\BorrowService;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
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
                Forms\Components\Select::make('user_id')
                    ->label('Borrower')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
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
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Borrower')
                    ->description(fn(Borrow $record) => $record->user->yearLevelAndCourse),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('M j, h:i A'),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime('M j, h:i A'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Submitted At'),
                Tables\Columns\TextColumn::make('books.title')
                    ->badge()
            ])
            ->defaultGroup(
                Group::make('status')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->status->getTitle())
                    ->getDescriptionFromRecordUsing(fn($record) => $record->status->description())
            )
            ->actions([
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->button()
                    ->color('info')
                    ->visible(fn($record) => $record->status->value != 'Returned')
                    ->form(fn($record) => [
                        Forms\Components\Group::make([
                            Placeholder::make('currStatus')
                                ->label('Current Status')
                                ->content($record->status->getTitle()),
                            Select::make('status')
                                ->label('New Status')
                                ->options(BorrowStatus::class)
                                ->native(false)
                                ->disableOptionWhen(fn(string $value): bool =>  $value === $record->status->value)
                                ->required()
                        ])->columns(2)
                    ])
                    ->action(function (array $data, Borrow $record) {

                        BorrowService::updateStatus($record, $data['status']);
                        Notification::make()
                            ->success()
                            ->title('Request status successfully updated!')
                            ->send();
                    }),
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
            // 'create' => Pages\CreateBorrow::route('/create'),
            'view' => Pages\ViewBorrow::route('/{record}'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }
}
