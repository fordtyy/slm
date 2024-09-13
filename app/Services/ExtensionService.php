<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionStatus;
use App\Models\Extension;

class ExtensionService
{
    public static function updateStatus(Extension $extension, string $status): void
    {
        $data = ['status' => $status];

        if ($status === ExtensionStatus::APPROVED->value) {
            $borrow =  $extension->borrow;

            $borrow->update([
                'due_date' => $borrow->due_date->addWeekdays($extension->number_of_days),
                'status' => BorrowStatus::EXTENDED
            ]);
        }

        $extension->update($data);
    }
}
