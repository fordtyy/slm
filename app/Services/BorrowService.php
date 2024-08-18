<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Events\BorrowApproved;
use App\Events\BorrowReleased;
use App\Events\BorrowStatusUpdate;
use App\Models\Borrow;

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
    public static function updateStatus(Borrow $borrow, string $status): void
    {
        $data = ['status' => $status];

        if ($borrow->status === BorrowStatus::RELEASED) {
            $now = now();

            $data['start_date'] = $now;
            $data['due_date'] = $now->copy()->addDays(3);
        }

        $borrow->update($data);

        // Trigger an event that will send an email notification to borrower.
        BorrowStatusUpdate::dispatch($borrow);
    }
}
