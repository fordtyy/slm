<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionStatus;
use App\Events\ExtensionStatusUpdate;
use App\Models\Extension;

class ExtensionService
{
    public static function updateStatus(Extension $extension, string $status): void
    {
        $data = ['status' => $status];

        if ($status === ExtensionStatus::APPROVED->value) {
            $borrow =  $extension->borrow;

            BorrowService::updateStatus($borrow, BorrowStatus::EXTENDED->value, $extension->number_of_days->value);
        }

        $extension->update($data);

        ExtensionStatusUpdate::dispatch($extension);
    }
}
