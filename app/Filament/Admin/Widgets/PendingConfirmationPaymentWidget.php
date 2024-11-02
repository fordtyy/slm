<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingConfirmationPaymentWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 8;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()->where('status', PaymentStatus::PENDING_CONFIRMATION)
            )
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->date(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->visible(fn($record) => $record->method === PaymentMethod::GCASH)
                    ->infolist([
                        ImageEntry::make('supporting_document')
                            ->alignJustify()
                            ->hiddenLabel()
                            ->extraAttributes([
                                'class' => 'justify-center mb-4'
                            ]),
                    ])
                    ->color('info')
                    ->modalHeading('Review Supporting Document')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-information-circle')
                    ->modalDescription('Check if the information provided by borrower is correct before confirming the payment.')
                    ->modalSubmitActionLabel('Confirm Payment')
                    ->action(function (Payment $record) {
                        PaymentService::approve($record);
                    }),
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->visible(fn($record) => in_array($record->status, [PaymentStatus::PENDING, PaymentStatus::PENDING_CONFIRMATION]))
                    ->action(function (Payment $record) {
                        PaymentService::approve($record);
                    }),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->iconButton()
                    ->visible(fn($record) => in_array($record->status, [PaymentStatus::PENDING, PaymentStatus::PENDING_CONFIRMATION]))
                    ->requiresConfirmation()
                    ->modalHeading('Reject Payment')
                    ->modalDescription('Provide a valid reason why you reject this payment.')
                    ->form([
                        Textarea::make('remarks')
                            ->required()
                    ])
                    ->action(function (Payment $record, array $data) {
                        PaymentService::reject($record, $data['remarks']);
                    }),
            ]);
    }
}
