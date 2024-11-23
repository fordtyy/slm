<?php

namespace App\Filament\Account\Resources;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Account\Resources\BorrowResource\Pages;
use App\Filament\Account\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use App\Models\Extension;
use App\Models\Penalty;
use App\Models\User;
use App\Services\BorrowService;
use App\Services\ExtensionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'code';

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('user_id', Auth::id()))
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label('Request Code'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
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
                    ->slideOver()
                    ->form([
                        Forms\Components\ToggleButtons::make('number_of_days')
                            ->options(ExtensionDays::class)
                            ->inline()
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->required(),
                        Forms\Components\Placeholder::make('notice')
                            ->content('By submitting, you agree that you need to pay 15 pesos fee multiply
                                         to the number of days you want to extend your request.')
                    ])
                    ->modalWidth(MaxWidth::Large)
                    ->modalFooterActionsAlignment(Alignment::Right)
                    ->action(function (array $data, Borrow $record) {
                        $data['status'] = ExtensionStatus::PENDING;

                        $data['fee'] = $data['number_of_days'] * 15;

                        $extension = $record->extensions()->create($data);

                        $extension->payment()->create([
                            'amount' => $extension->fee,
                            'status' => PaymentStatus::PENDING
                        ]);

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
                    ->visible(fn($record) => $record->extensions()->exists())
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
                        Infolists\Components\TextEntry::make('extension.fee')
                            ->label('Fee')
                            ->inlineLabel()
                            ->money('PHP'),
                        Infolists\Components\TextEntry::make('extension.reason')
                            ->inlineLabel()
                            ->label('Reason'),
                    ])
                    ->modalWidth(MaxWidth::Large)
                    ->slideOver()
                    ->modalHeading('View Request Extension Details')
                    ->modalCancelAction(false)
                    ->modalSubmitActionLabel('Close')
                    ->modalFooterActionsAlignment(Alignment::Right),
                Tables\Actions\ViewAction::make()
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
            Infolists\Components\Section::make('Details')
                ->description('Information of this request')
                ->collapsible()
                ->schema([
                    Infolists\Components\TextEntry::make('code')
                        ->inlineLabel()
                        ->label('Code'),
                    Infolists\Components\TextEntry::make('status')
                        ->inlineLabel()
                        ->label('Status')
                        ->badge(),
                    Infolists\Components\TextEntry::make('released_date')
                        ->inlineLabel()
                        ->dateTime()
                        ->label('Released Date'),
                    Infolists\Components\TextEntry::make('due_date')
                        ->inlineLabel()
                        ->dateTime()
                        ->label('Due Date'),
                ]),
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
                        ->grid()
                        ->hiddenLabel()
                        ->schema([
                            Infolists\Components\TextEntry::make('code'),
                            Infolists\Components\TextEntry::make('number_of_days'),
                            Infolists\Components\TextEntry::make('fee')->money('PHP'),
                            Infolists\Components\TextEntry::make('status')->badge(),
                            Infolists\Components\TextEntry::make('reason'),
                            Infolists\Components\Actions::make([
                                Infolists\Components\Actions\Action::make('pay_extension')
                                    ->label('Pay Extension')
                                    ->visible(fn($record) => $record->status === ExtensionStatus::PENDING)
                                    ->fillForm(fn($record) => [
                                        'amount' => $record->fee
                                    ])
                                    ->form([
                                        Forms\Components\Group::make([
                                            Forms\Components\Group::make([
                                                Forms\Components\Hidden::make('amount')
                                                    ->default(fn($record) => $record->fee),
                                                Forms\Components\Placeholder::make('amount_display')
                                                    ->label('Amount')
                                                    ->content(fn(Get $get) => $get('amount')),
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

                                        $record->payment->update($data);

                                        ExtensionService::updateStatus($record, ExtensionStatus::PAYMENT_SUBMITTED->value);                                    }),
                            ]),
                        ])
                        ->columns(3)

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

    public static function shouldRegisterNavigation(): bool
    {
        return !in_array(Request::route()->getName(), ['filament.account.pages.category-preferred', 'filament.account.pages.author-preferred']);
    }
}
