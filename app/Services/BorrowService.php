<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Events\BorrowApproved;
use App\Events\BorrowReleased;
use App\Events\BorrowStatusUpdate;
use App\Mail\BorrowRequestDueDateReminderMail;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Borrow;
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

            $data['start_date'] = $now;
            $data['due_date'] = $now->copy()->addWeekdays(10);
        }

        if (BorrowStatus::EXTENDED->value === $status) {
            $date = $borrow->due_date;

            $data['due_date'] = $date->copy()->addWeekdays($extension);
        }

        $borrow->update($data);

        if ($status === BorrowStatus::RELEASED->value) {
          $book = BookBorrow::where("borrow_id", $borrow->id)->first();
          Book::where('id', $book->book_id)->decrement('copies');
        }

        if ($status === BorrowStatus::RETURNED->value) {
          $book = BookBorrow::where("borrow_id", $borrow->id)->first();
          Book::where('id', $book->book_id)->increment('copies');
        }

        // Trigger an event that will send an email notification to borrower.
        BorrowStatusUpdate::dispatch($borrow);
    }

    public static function sendDueNotification()
    {
        $borrows = Borrow::where('status', BorrowStatus::RELEASED)
            ->whereDoesntHave('extension')
            ->whereNotNull('due_date')
            ->whereDoesntHave('dateLogs', fn($query) => $query->whereDate('date', now()->today()))
            ->get();

        $borrows->filter(
            fn($borrow) =>
            $borrow->due_date->subWeekdays(3)->gte(now())
        )
            ->each(function ($borrow) {

                $borrower = $borrow->user;

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

                $borrow->dateLogs()->create(['date' => now()]);
            });
    }
}
