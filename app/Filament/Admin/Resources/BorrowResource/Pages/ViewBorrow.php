<?php

namespace App\Filament\Admin\Resources\BorrowResource\Pages;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionDays;
use App\Enums\ExtensionStatus;
use App\Filament\Admin\Resources\BorrowResource;
use App\Models\Borrow;
use App\Services\BorrowService;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Auth;

class ViewBorrow extends ViewRecord
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
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
            Actions\Action::make('reject')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
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
            Actions\Action::make('release')
                ->color('info')
                ->icon('heroicon-o-bars-arrow-up')
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
            Actions\Action::make('return')
                ->color('info')
                ->icon('heroicon-o-bars-arrow-down')
                ->requiresConfirmation()
                ->visible(fn($record) => in_array($record->status, [BorrowStatus::RELEASED, BorrowStatus::EXTENDED]))
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
            Actions\Action::make('add_extension')
                ->icon('heroicon-o-folder-plus')
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
        ];
    }
}
