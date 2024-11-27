<?php

namespace App\Listeners;

use App\Events\ExtensionStatusUpdate;
use App\Mail\ExtensionStatusUpdateMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendExtensionStatusEmailNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExtensionStatusUpdate $event): void
    {
        $extension = $event->extension;
        $borrower = $extension->borrow->user;

        Mail::to($borrower->email, $borrower->name)
            ->send(new ExtensionStatusUpdateMail($extension));
    }
}
