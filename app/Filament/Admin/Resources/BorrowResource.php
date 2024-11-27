<?php

namespace App\Filament\Admin\Resources;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\BorrowResource\Pages;
use App\Filament\Admin\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use App\Models\Extension;
use App\Services\BorrowService;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Requests';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Borrower' => $record->user->name,
            'Requested On' => $record->created_at->diffForHumans()
        ];
    }

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

                Tables\Columns\TextColumn::make('released_date')
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
                    ->visible(fn($record) => in_array($record->status, [BorrowStatus::APPROVED, BorrowStatus::PENDING]))
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
                    ->visible(fn($record) => in_array($record->status, [BorrowStatus::RELEASED, BorrowStatus::EXTENDED]) || ($record->status === BorrowStatus::DUE && !$record->hasPendingPenalties()))
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

                        $record->extensions()->create($data);

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
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return  $infolist->schema([
            Infolists\Components\Section::make('Penalties')
                ->visible(fn($record) => $record->penalties()->exists())
                ->description('List of penalties of this requested.')
                ->aside()
                ->schema([
                    Infolists\Components\RepeatableEntry::make('penalties')
                        ->hiddenLabel()
                        ->schema([
                            Infolists\Components\TextEntry::make('created_at')
                                ->label('Charge On')
                                ->dateTime(),
                            Infolists\Components\TextEntry::make('amount'),
                            Infolists\Components\TextEntry::make('status')->badge(),
                            Infolists\Components\TextEntry::make('remarks'),

                        ])->columns(4)

                ]),
            Infolists\Components\Section::make('Request Details')
                ->schema([
                    Infolists\Components\TextEntry::make('code')
                        ->label('Code'),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->badge(),
                    Infolists\Components\TextEntry::make('released_date')
                        ->dateTime()
                        ->label('Released Date'),
                    Infolists\Components\TextEntry::make('due_date')
                        ->dateTime()
                        ->label('Due Date'),

                    Infolists\Components\TextEntry::make('borrower')
                        ->label('Borrower')
                        ->getStateUsing(fn($record) => $record->user->name)
                        ->tooltip(fn($record) => $record->user->yearLevelAndCourse)
                        ->hintAction(
                            fn(Borrow $record) =>
                            Infolists\Components\Actions\Action::make('view_info')
                                ->label('Borrower Info')
                                ->icon('heroicon-o-information-circle')
                                ->iconButton()
                                ->infolist([
                                    Infolists\Components\Group::make([
                                        Infolists\Components\TextEntry::make('name')
                                            ->state($record->user->name)
                                            ->suffix(" ({$record->user->yearLevelAndCourse})"),
                                        Infolists\Components\TextEntry::make('penalties')
                                            ->state("Total: " . $record->penalties()->count())
                                    ])->columns(2),

                                ])
                        ),
                ])
                ->columns(5),
            Infolists\Components\Section::make('Books')
                ->description('List of books of this request')
                ->collapsible()
                ->schema([
                    Infolists\Components\RepeatableEntry::make('books')
                        ->hiddenLabel()
                        ->contained(false)
                        ->schema([
                            Infolists\Components\TextEntry::make('title'),
                            Infolists\Components\TextEntry::make('authors')
                                ->formatStateUsing(fn($record) => $record->authorsName),
                            Infolists\Components\TextEntry::make('category.name'),
                            Infolists\Components\TextEntry::make('tags.name')
                                ->badge()
                                ->getStateUsing(fn($record) => $record->tagsName)
                        ])->columns(4)
                ]),
            Infolists\Components\Section::make('Extensions')
                ->description('List of extensions requested.')
                ->visible(fn(Borrow $record): bool => $record->extensions()->exists())
                ->collapsible()
                ->schema([
                    Infolists\Components\RepeatableEntry::make('extensions')
                        ->hiddenLabel()
                        ->schema([
                            Infolists\Components\TextEntry::make('code'),
                            Infolists\Components\TextEntry::make('number_of_days'),
                            Infolists\Components\TextEntry::make('status')->badge(),
                            Infolists\Components\TextEntry::make('reason'),
                            Infolists\Components\Actions::make([
                                Infolists\Components\Actions\Action::make('review_payment')
                                    ->label('Review Payment')
                                    ->visible(fn($record) => $record->status === ExtensionStatus::PAYMENT_SUBMITTED && $record->payment?->method === PaymentMethod::GCASH)
                                    ->form([
                                        Forms\Components\Group::make([
                                            Forms\Components\Group::make([
                                                Forms\Components\TextInput::make('amount')
                                                    ->label('Amount')
                                                    ->default(fn($record) => $record->fee)
                                                    ->prefix('P')
                                                    ->numeric(),
                                                Forms\Components\ToggleButtons::make('method')
                                                    ->label('Payment Method')
                                                    ->options(PaymentMethod::class)
                                                    ->inline()
                                                    ->live(),

                                                Forms\Components\FileUpload::make('supporting_document')
                                                    ->required()
                                                    ->visible(fn(Get $get) => $get('method') == PaymentMethod::GCASH->value)
                                                    ->label('Supporting Document'),
                                            ]),
                                            Forms\Components\Placeholder::make('qr_code')
                                                ->label('Qr Code')
                                                ->visible(fn(Get $get) => $get('method') == PaymentMethod::GCASH->value)
                                                ->content(new HtmlString("<img src=" .  asset("/images/qr_code.jpg") . " class='w-1/2' alt='QR Code'/>")),
                                        ])->columns(),
                                    ])
                                    ->action(function (array $data, Extension $record) {
                                        $data['paid_at'] = now();
                                        $data['status'] = PaymentStatus::PENDING_CONFIRMATION;

                                        $record->payment()->update($data);
                                    }),
                                Infolists\Components\Actions\Action::make('approve')
                                    ->label('Approve')
                                    ->visible(fn($record) => $record->status === ExtensionStatus::PAYMENT_SUBMITTED  && $record->payment?->method === PaymentMethod::IN_COUNTER)
                                    ->form([
                                        Forms\Components\Group::make([
                                            Forms\Components\Group::make([
                                                Forms\Components\TextInput::make('amount')
                                                    ->label('Amount')
                                                    ->default(fn($record) => $record->fee)
                                                    ->prefix('P')
                                                    ->numeric(),
                                                Forms\Components\ToggleButtons::make('method')
                                                    ->label('Payment Method')
                                                    ->options(PaymentMethod::class)
                                                    ->inline()
                                                    ->live(),

                                                Forms\Components\FileUpload::make('supporting_document')
                                                    ->required()
                                                    ->visible(fn(Get $get) => $get('method') == PaymentMethod::GCASH->value)
                                                    ->label('Supporting Document'),
                                            ]),
                                            Forms\Components\Placeholder::make('qr_code')
                                                ->label('Qr Code')
                                                ->visible(fn(Get $get) => $get('method') == PaymentMethod::GCASH->value)
                                                ->content(new HtmlString("<img src=" .  asset("/images/qr_code.jpg") . " class='w-1/2' alt='QR Code'/>")),
                                        ])->columns(),
                                    ])
                                    ->action(function (array $data, Extension $record) {
                                        $data['paid_at'] = now();
                                        $data['status'] = PaymentStatus::PENDING_CONFIRMATION;

                                        $record->payment()->update($data);
                                    }),
                            ]),
                        ])
                        ->columns(5)

                ]),

        ])->columns(4);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ExtensionsRelationManager::class,
            RelationManagers\PaymentRelationManager::class
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
