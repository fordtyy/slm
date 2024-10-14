<?php

namespace App\Filament\Account\Resources;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Filament\Account\Resources\BorrowResource\Pages;
use App\Filament\Account\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use App\Models\User;
use App\Services\BorrowService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Request;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationGroup = 'Request';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label('Request Code'),
                Tables\Columns\TextColumn::make('books.title')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->color('white')
                    ->formatStateUsing(function ($record) {
                        if ($record->status->value === 'Cancel') {
                            return 'Cancelled';
                        } else {
                            return $record->status->value;
                        }
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Requested At')
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BorrowStatus::class)
                    ->native(false)
            ])
            ->defaultGroup(
                Group::make('status')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn($record) => $record->status->getTitle())
                    ->getDescriptionFromRecordUsing(fn($record) => $record->status->description())
            )
            ->actions([
                Tables\Actions\Action::make('Cancel')
                    ->visible(fn($record) => $record->status->value == 'Pending')
                    ->label('Cancel')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function ($record) {
                        BorrowService::updateStatus($record, 'Cancel');
                        Notification::make()
                            ->success()
                            ->title('Book request successfully cancelled!')
                            ->send();
                    }),
                Tables\Actions\Action::make('request_extension')
                    ->icon('heroicon-o-folder-plus')
                    ->iconButton()
                    ->visible(fn($record) => $record->canBeExtended())
                    ->form([
                        Forms\Components\ToggleButtons::make('number_of_days')
                            ->options(ExtensionDays::class)
                            ->inline()
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->required(),
                    ])
                    ->action(function (array $data, Borrow $record) {
                        $data['status'] = ExtensionStatus::PENDING;

                        $extension = $record->extension()->create($data);

                        Notification::make()
                            ->success()
                            ->title('Extension Request created!')
                            ->body("Code: [" . $extension->code . "]")
                            ->sendToDatabase($record->user)
                            ->send();

                        Notification::make()
                            ->info()
                            ->title('New Extension Request Added')
                            ->body("Code: [" . $extension->code . "]")
                            ->sendToDatabase(User::where('type', 'admin')->get());
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
                            ->label('Reason'),
                    ])
                    ->modalCancelAction(false)
                    ->modalSubmitActionLabel('Close')
                    ->modalFooterActionsAlignment(Alignment::Right),
                Tables\Actions\ViewAction::make()
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return  $infolist->schema([

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
            'create' => Pages\CreateBorrow::route('/create'),
            'view' => Pages\ViewBorrow::route('/{record}'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
      return !in_array(Request::route()->getName(), ['filament.account.pages.category-preferred', 'filament.account.pages.author-preferred']);
    }
}
