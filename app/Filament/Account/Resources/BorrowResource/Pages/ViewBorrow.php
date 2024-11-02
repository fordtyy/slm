<?php

namespace App\Filament\Account\Resources\BorrowResource\Pages;

use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PenaltyStatus;
use App\Filament\Account\Resources\BorrowResource;
use App\Models\Borrow;
use App\Models\User;
use App\Services\BorrowService;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\HtmlString;

class ViewBorrow extends ViewRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('extend')
                ->icon('heroicon-o-folder-plus')
                ->iconPosition(IconPosition::After)
                ->outlined()
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
                        'reference' =>  $record->code,
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
            Actions\Action::make('pay_penalty')
                ->visible(fn(Borrow $record) => $record->penalties()->where('status', PenaltyStatus::PENDING)->exists())
                ->fillForm(fn(Borrow $record): array => [
                    'amount' => $record->penalties()->sum('amount')
                ])
                ->form([
                    Forms\Components\Hidden::make('amount'),
                    Forms\Components\Group::make([
                        Forms\Components\Group::make([
                            Forms\Components\Placeholder::make('amount_display')
                                ->label('Amount')
                                ->content(fn(Get $get) => $get('amount')),
                            Forms\Components\ToggleButtons::make('method')
                                ->label('Payment Method')
                                ->options(PaymentMethod::class)
                                ->inline()
                                ->required()
                                ->live(),
                            Forms\Components\FileUpload::make('supporting_document')
                                ->required()
                                ->visible(fn(Get $get) => $get('method') == PaymentMethod::GCASH->value)
                                ->label('Supporting Document'),
                        ]),
                        Forms\Components\Placeholder::make('qr_code')
                            ->label('Qr Code')
                            ->visible(fn(Get $get) => $get('method') == PaymentMethod::GCASH->value)
                            ->content(new HtmlString("<img src='/images/qr_code.jpg' class='w-1/2' alt='QR Code'/>")),
                    ])->columns(),
                ])->action(function (array $data, Borrow $record) {
                    $data['paid_at'] = now();
                    $data['status'] = PaymentStatus::PENDING_CONFIRMATION;
                    $data['reference'] = $record->code;

                    $record->payment()->create($data);

                    $record->penalties()->update([
                        'status' => PenaltyStatus::ON_PROCESS
                    ]);
                }),
            Actions\Action::make('Cancel')
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
                })
        ];
    }
}
