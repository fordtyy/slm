<?php

namespace App\Listeners;

use App\Events\BorrowStatusUpdate;
use App\Mail\BorrowStatusUpdateMail;
use Illuminate\Support\Facades\Mail;

class SendBorrowStatusUpdateEmailNotification
{
    /**
     * Handle the event.
     */
    public function handle(BorrowStatusUpdate $event): void
    {
        $request = $event->borrow;

        $borrower = $request->user;

        Mail::to($borrower->email, $borrower->name)
            ->send(new BorrowStatusUpdateMail($request));
    }
}
