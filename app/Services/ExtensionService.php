<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionStatus;
use App\Enums\PaymentStatus;
use App\Events\ExtensionStatusUpdate;
use App\Models\Borrow;
use App\Models\Extension;
use App\Models\User;
use Filament\Notifications\Notification;

class ExtensionService
{
    public static function create(array $data, Borrow $borrow)
    {
        $data['status'] = ExtensionStatus::PENDING;

        $data['fee'] = $data['number_of_days'] * 15;

        $extension = $borrow->extensions()->create($data);

        $borrow->payments()->create([
            'reference' =>  $borrow->code,
            'amount' => $extension->fee,
            'status' => PaymentStatus::PENDING,
            'source_code' => $extension->code
        ]);

        Notification::make()
            ->success()
            ->title('Extension Request created!')
            ->body("Code: [" . $extension->code . "]")
            ->sendToDatabase($borrow->user)
            ->send();

        Notification::make()
            ->info()
            ->title('New Extension Request Added')
            ->body("Code: [" . $extension->code . "]")
            ->sendToDatabase(User::where('type', 'admin')->get());
        self::updateStatus($extension, ExtensionStatus::PENDING->value);
    }

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
