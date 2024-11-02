<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\BorrowStatus;
use App\Models\Borrow;
use App\Services\BorrowService;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class PendingBorrowRequest extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 8;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pending Borrow Request')
            ->query(Borrow::with(['books', 'user'])
                ->where('status', BorrowStatus::PENDING->value))
            ->columns([
                TextColumn::make('code')
                    ->label('Request Code'),
                TextColumn::make('user.name')
                    ->label('Borrower')
                    ->description(fn(Borrow $record) => $record->user->yearLevelAndCourse),
                TextColumn::make('books.title')
                    ->badge(),
                TextColumn::make('created_at')
                    ->date()
                    ->label('Requested At')
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
            ])
            ->defaultPaginationPageOption(5);
    }
}
