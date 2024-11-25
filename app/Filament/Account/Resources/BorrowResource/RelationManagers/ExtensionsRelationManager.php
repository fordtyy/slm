<?php

namespace App\Filament\Account\Resources\BorrowResource\RelationManagers;

use App\Enums\ExtensionStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Extension;
use App\Models\Payment;
use App\Services\ExtensionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ExtensionsRelationManager extends RelationManager
{
    protected static string $relationship = 'extensions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->description('List of extensions requested.')
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('number_of_days'),
                Tables\Columns\TextColumn::make('fee')->money('PHP'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('reason'),
            ])
            ->actions([
                Tables\Actions\Action::make('pay_extension')
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

                        Payment::where('source_code', $record->code)->update($data);

                        ExtensionService::updateStatus($record, ExtensionStatus::PAYMENT_SUBMITTED->value);
                    })
            ]);
    }
}
