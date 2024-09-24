<?php

namespace App\Filament\Admin\Resources;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Filament\Admin\Resources\BorrowResource\Pages;
use App\Filament\Admin\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use App\Services\BorrowService;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

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
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status == BorrowStatus::PENDING)
                    ->action(function (Borrow $record) {
                        BorrowService::updateStatus($record, BorrowStatus::APPROVED->value);
                        Notification::make()
                            ->info()
                            ->title('Approved Borrow Request 👍')
                            ->body("Admin approved your borrow request. Please check your email for more info.")
                            ->sendToDatabase($record->user);
                        Notification::make()
                            ->success()
                            ->title('Success Approval')
                            ->body("Borrow Request [" . $record->code . "] approved successfully")
                            ->sendToDatabase(Auth::user())
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status == BorrowStatus::PENDING)
                    ->action(function (Borrow $record) {
                        BorrowService::updateStatus($record, BorrowStatus::REJECTED->value);
                        Notification::make()
                            ->info()
                            ->title('Rejected Borrow Request 👎')
                            ->body("Admin reject your borrow request. Please check your email for more info.")
                            ->sendToDatabase($record->user);
                        Notification::make()
                            ->success()
                            ->title("Success Rejection")
                            ->body("Borrow Request [" . $record->code . "] rejected successfully")
                            ->sendToDatabase(Auth::user())
                            ->send();
                    }),
                Tables\Actions\Action::make('release')
                    ->color('info')
                    ->icon('heroicon-o-bars-arrow-up')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->status == BorrowStatus::APPROVED)
                    ->action(function (Borrow $record) {
                        BorrowService::updateStatus($record, BorrowStatus::RELEASED->value);
                        Notification::make()
                            ->info()
                            ->title('Released Borrow Request 🚀')
                            ->body("Admin released your borrow request. Please check your email for more info.")
                            ->sendToDatabase($record->user);
                        Notification::make()
                            ->success()
                            ->title("Success Released")
                            ->body("Borrow Request [" . $record->code . "] released successfully")
                            ->sendToDatabase(Auth::user())
                            ->send();
                    }),
                Tables\Actions\Action::make('return')
                    ->color('info')
                    ->icon('heroicon-o-bars-arrow-down')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->visible(fn($record) => in_array($record->status, [BorrowStatus::RELEASED, BorrowStatus::EXTENDED]))
                    ->action(function (Borrow $record) {
                        BorrowService::updateStatus($record, BorrowStatus::RETURNED->value);
                        Notification::make()
                            ->info()
                            ->title('Returned Borrow Request 🎉')
                            ->body("Borrowed books marked as returned.")
                            ->sendToDatabase($record->user);
                        Notification::make()
                            ->success()
                            ->title("Success Released")
                            ->body("Borrow Request [" . $record->code . "] released successfully")
                            ->sendToDatabase(Auth::user())
                            ->send();
                    }),
                Tables\Actions\Action::make('add_extension')
                    ->icon('heroicon-o-folder-plus')
                    ->iconButton()
                    ->form([
                        Forms\Components\ToggleButtons::make('number_of_days')
                            ->options(ExtensionDays::class)
                            ->inline()
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->required(),
                    ])
                    ->visible(fn($record) => $record->canBeExtended())
                    ->action(function (array $data, Borrow $record) {
                        $data['status'] = ExtensionStatus::PENDING;

                        $record->extension()->create($data);

                        Notification::make()
                            ->success()
                            ->title('Extension Request Created!')
                            ->body('Admin extended your Borrow Request. Please check your email for more info.')
                            ->sendToDatabase($record->user);

                        Notification::make()
                            ->success()
                            ->title('Extension Request created!')
                            ->body('Added Extension Request for Borrow Request [' . $record->code . ']')
                            ->sendToDatabase(Auth::user())
                            ->send();
                    }),
                Tables\Actions\Action::make('view_request_extension')
                    ->icon('heroicon-o-folder-open')
                    ->iconButton()
                    ->visible(fn($record) => $record->extension()->exists())
                    ->infolist([

                        Infolists\Components\TextEntry::make('extension.code')
                            ->label('Code')
                            ->inlineLabel(),
                        Infolists\Components\TextEntry::make('extension.number_of_days')
                            ->label('Number of Days')
                            ->inlineLabel(),
                        Infolists\Components\TextEntry::make('extension.status')
                            ->label('Status')
                            ->inlineLabel()
                            ->badge(),
                        Infolists\Components\TextEntry::make('extension.reason')
                            ->inlineLabel()
                            ->label('Reason')
                    ])
                    ->modalCancelAction(false)
                    ->modalSubmitActionLabel('Close')
                    ->modalFooterActionsAlignment(Alignment::Right),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return  $infolist->schema([
            Infolists\Components\Section::make('Request Details')->schema([
                Infolists\Components\TextEntry::make('code')
                    ->label('Code'),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                Infolists\Components\TextEntry::make('start_date')
                    ->dateTime()
                    ->label('Start Date'),
                Infolists\Components\TextEntry::make('due_date')
                    ->dateTime()
                    ->label('Due Date'),

                Infolists\Components\Section::make('Books')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('books')

                            ->hiddenLabel()
                            ->schema([
                                Infolists\Components\TextEntry::make('title'),
                                Infolists\Components\TextEntry::make('authors')
                                    ->formatStateUsing(fn($record) => $record->authorsName),
                                Infolists\Components\TextEntry::make('category.name'),
                                Infolists\Components\TextEntry::make('tags.name')
                                    ->badge()
                                    ->getStateUsing(fn($record) => $record->tagsName)
                            ])->columns(4)
                    ])
            ])->columnSpan(3)
                ->columns(4),
            Infolists\Components\Section::make('Borrower')
                ->schema([

                    Infolists\Components\TextEntry::make('user.name')
                        ->label('Name'),
                    Infolists\Components\TextEntry::make('user.course.name')
                        ->label('Course'),
                    Infolists\Components\TextEntry::make('user.yearLevel.name')
                        ->label('Level'),


                ])->columnSpan(1)

        ])->columns(4);
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
