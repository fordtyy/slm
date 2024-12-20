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
use App\Services\ExtensionService;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
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
                ->visible(fn($record) => $record->canBeExtended() && !Auth::user()->hasPenalties())
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
                    ExtensionService::create($data, $record);
                }),
            Actions\Action::make('pay_penalty')
                ->visible(fn(Borrow $record) => $record->penalties()->where('status', PenaltyStatus::PENDING)->exists())
                ->fillForm(fn(Borrow $record): array => [
                    'amount' => $record->penalties()->where('status', PenaltyStatus::PENDING)->sum('amount')
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
                            ->content(new HtmlString("<img src=" .  asset("/images/qr_code.jpg") . " class='w-1/2' alt='QR Code'/>")),
                    ])->columns(),
                ])->action(function (array $data, Borrow $record) {
                    $data['paid_at'] = now();
                    $data['status'] = PaymentStatus::PENDING_CONFIRMATION;
                    $data['reference'] = $record->code;
                    $data['source_code'] = $record->penalties()
                        ->where('status', PenaltyStatus::PENDING)
                        ->pluck('code')
                        ->join(', ');

                    $record->payments()->create($data);

                    $record->penalties()->where('status', PenaltyStatus::PENDING)->update([
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
