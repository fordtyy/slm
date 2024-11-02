<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasIcon, HasColor, HasDescription
{
    case PENDING = 'Pending';
    case PENDING_CONFIRMATION = 'Pending Confirmation';
    case COMPLETED = 'Completed';
    case REJECTED = 'Rejected';

    public function getDescription(): ?string
    {
        return match ($this) {
            $this::PENDING => 'Pending Payment',
            $this::PENDING_CONFIRMATION => 'Payment need to review',
            $this::COMPLETED => 'Payment reviewed and completed.',
            $this::REJECTED => 'Payment rejected.'
        };
    }

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            $this::PENDING => 'heroicon-o-clock',
            $this::PENDING_CONFIRMATION => 'heroicon-o-document-magnifying-glass',
            $this::COMPLETED =>  'heroicon-o-check-badge',
            $this::REJECTED =>  'heroicon-o-no-symbol',
        };
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            $this::PENDING => 'gray',
            $this::PENDING_CONFIRMATION => 'info',
            $this::COMPLETED => 'success',
            $this::REJECTED => 'danger',
        };
    }
}
