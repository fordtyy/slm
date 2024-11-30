<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Enums\PenaltyStatus;
use App\Events\BorrowApproved;
use App\Events\BorrowReleased;
use App\Events\BorrowStatusUpdate;
use App\Mail\BorrowRequestDueDateReminderMail;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Borrow;
use Carbon\Carbon;
use Filament\Notifications;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class BorrowService
{
    /**
     * Update the borrow status and send email notification to the borrower.
     *
     * @param \App\Models\Borrow $borrow request need to update
     * @param string $status new status
     *
     * @return void
     */
    public static function updateStatus(Borrow $borrow, string $status, int $extension = 0): void
    {
        $data = ['status' => $status];

        if (BorrowStatus::RELEASED->value === $status) {
            $now = now();

            $data['released_date'] = $now;
            $data['due_date'] = $now->copy()->addWeekdays(10);
        }

        if (BorrowStatus::EXTENDED->value === $status) {
            $date = $borrow->due_date;

            $data['due_date'] = $date->copy()->addWeekdays($extension);
        }

        if ($status === BorrowStatus::RELEASED->value) {
            $book = BookBorrow::where("borrow_id", $borrow->id)->first();
            Book::where('id', $book->book_id)->decrement('copies');
        }

        if ($status === BorrowStatus::RETURNED->value) {
            $book = BookBorrow::where("borrow_id", $borrow->id)->first();
            Book::where('id', $book->book_id)->increment('copies');
        }

        $borrow->update($data);

        // Trigger an event that will send an email notification to borrower.
        BorrowStatusUpdate::dispatch($borrow);
    }

    public static function sendDueNotification()
    {
        $borrows = Borrow::whereIn('status', [
            BorrowStatus::RELEASED,
            BorrowStatus::EXTENDED,
            BorrowStatus::DUE,
        ])
            ->whereNotNull('due_date')
            ->whereDoesntHave('dateLogs', fn($query) => $query->whereDate('date', now()->today()))
            ->get();

        $borrows->filter(
            fn($borrow) =>
            $borrow->due_date->subWeekdays(3)->gte(now()) || now()->gte($borrow->due_date)
        )
            ->each(function (Borrow $borrow) {
                $borrower = $borrow->user;

                if (now()->gte($borrow->due_date)) {
                    if ($borrow->status === BorrowStatus::RELEASED) {
                        self::updateStatus($borrow, BorrowStatus::DUE->value);
                    }

                    $penaltyData = [
                        'user_id' => $borrower->id,
                        'status' => PenaltyStatus::PENDING
                    ];

                    if ($borrow->due_date->isToday()) {
                        $penaltyData['amount'] = 50;
                        $penaltyData['remarks'] = 'Request is due.';
                    } else {
                        $penaltyData['amount'] = 5;
                        $penaltyData['remarks'] = 'Penalty extension.';
                    }

                    $borrow->penalties()->create($penaltyData);
                } else {
                    Mail::to($borrower->email, $borrower->name)
                        ->send(new BorrowRequestDueDateReminderMail($borrow));

                    Notification::make()
                        ->info()
                        ->title('Due Date Reminder 🕒')
                        ->body("Borrow Request [" . $borrow->code . "] is due on " . $borrow->due_date->format('Y-m-d'))
                        ->sendToDatabase([$borrow->user])
                        ->actions([
                            Notifications\Actions\Action::make('view')
                                ->label('View Request')
                                ->url(route('filament.account.resources.borrows.view', $borrow))
                        ]);
                }

                $borrow->dateLogs()->create(['date' => now()]);
            });
    }
}
