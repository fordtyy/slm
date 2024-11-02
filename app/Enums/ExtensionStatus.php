<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ExtensionStatus: string implements HasLabel, HasIcon, HasColor
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case CANCEL = 'Cancel';
    case REJECTED = 'Rejected';
    case PAYMENT_SUBMITTED = 'Payment Submitted';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            $this::PENDING => 'heroicon-o-clock',
            $this::APPROVED => 'heroicon-o-bars-3-bottom-left',
            $this::PAYMENT_SUBMITTED => 'heroicon-o-check-badge',
            $this::CANCEL =>  'heroicon-o-check-x-circle',
            $this::REJECTED =>  'heroicon-o-no-symbol',
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            $this::PENDING,
            $this::CANCEL => 'gray',
            $this::APPROVED => 'info',
            $this::REJECTED => 'danger',
            $this::PAYMENT_SUBMITTED => 'success'
        };
    }
}
