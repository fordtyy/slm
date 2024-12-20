<?php

namespace App\Services;

use App\Enums\BorrowStatus;
use App\Enums\ExtensionStatus;
use App\Enums\PaymentStatus;
use App\Enums\PenaltyStatus;
use App\Models\Borrow;
use App\Models\Extension;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public static function approve(Payment $payment)
    {
        $payment->update([
            'status' => PaymentStatus::COMPLETED
        ]);

        $payable = $payment->payable;

        if ($payable instanceof Borrow) {
            $user = $payable->user;

            if (str($payment->source_code)->startsWith('ER')) {
                $extension = Extension::where('code', $payment->source_code)->first();
                ExtensionService::updateStatus($extension, ExtensionStatus::APPROVED->value);
            } elseif (str($payable->source_code)->startsWith('PE')) {
                $payable->penalties()->where('status', PenaltyStatus::ON_PROCESS)
                    ->update([
                        'status' => PenaltyStatus::RESOLVE
                    ]);
            }
        }

        Notification::make()
            ->success()
            ->title('Success Payment Confirmation!')
            ->body($payment->code . ' has been confirmed!')
            ->sendToDatabase(Auth::user())
            ->send();
        // Send Notification to the borrower
        Notification::make()
            ->success()
            ->title('Payment Confirmed!')
            ->body($payment->code . ' has been confirmed!')
            ->sendToDatabase($user);
    }

    public static function reject(Payment $payment, string $remarks)
    {
        $payment->update([
            'status' => PaymentStatus::REJECTED,
            'remarks' => $remarks
        ]);

        Notification::make()
            ->success()
            ->title('Payment Rejected!')
            ->body($payment->code . ' has been rejected!')
            ->sendToDatabase(Auth::user())
            ->send();
        $payable = $payment->payable;

        if ($payable instanceof Extension) {
            ExtensionService::updateStatus($payable, ExtensionStatus::REJECTED->value);

            $user = $payable->borrow->user;
        }

        if ($payable instanceof Borrow) {
            $user = $payable->user;
        }

        Notification::make()
            ->success()
            ->title('Payment Rejected!')
            ->body($payment->code . ' has been rejected!')
            ->sendToDatabase($user);
    }
}
