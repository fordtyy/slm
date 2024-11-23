<?php

namespace App\Filament\Account\Widgets;

use App\Filament\Account\Resources\BorrowResource;
use App\Models\Borrow;
use App\Services\BorrowService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class PendingBorrowRequests extends BaseWidget
{

    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';
    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(BorrowResource::getEloquentQuery()->where('status', '=', 'Pending')
                ->where('user_id', Auth::id()))
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('books.title'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->label('Submitted At'),

            ])
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
                    })
            ]);
    }
}
