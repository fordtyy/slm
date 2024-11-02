<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ExtensionStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;
use App\Models\Borrow;
use App\Models\Extension;
use App\Models\Payment;
use App\Services\PaymentService;
use Filament\Actions\Action;
use Filament\Actions\MountableAction;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Requests';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('reference')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('method'),
                Tables\Columns\TextColumn::make('paid_at')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label('Review')
                    ->visible(fn($record) => $record->method === PaymentMethod::GCASH)
                    ->infolist([
                        Infolists\Components\ImageEntry::make('supporting_document')
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
                    ->visible(fn($record) => $record->method === PaymentMethod::IN_COUNTER && in_array($record->status, [PaymentStatus::PENDING, PaymentStatus::PENDING_CONFIRMATION]))
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
                        Forms\Components\Textarea::make('remarks')
                            ->required()
                    ])
                    ->action(function (Payment $record, array $data) {
                        PaymentService::reject($record, $data['remarks']);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public function cancel()
    {
        dd("test");
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
