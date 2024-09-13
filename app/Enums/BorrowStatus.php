<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum BorrowStatus: string implements HasLabel, HasColor
{
    use IsKanbanStatus;
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case RELEASED = 'Released';
    case CANCEL = 'Cancel';
    case REJECTED = 'Rejected';
    case RETURNED = 'Returned';
    case EXTENDED = 'Extended';

    public function description(): string
    {
        return match ($this) {
            $this::PENDING => 'Request need to review.',
            $this::APPROVED => 'Request done review and ready to release',
            $this::RELEASED => 'Request is confirmed and books is release to borrower',
            $this::CANCEL => 'Request not continued by borrower.',
            $this::REJECTED => 'Request not approved for any reason.',
            $this::RETURNED => 'Request is successfully done.',
            $this::EXTENDED => 'Request extended by borrower',
        };
    }

    public function getLabel(): string|null
    {
        return $this->value;
    }

    public function getColor(): array|string|null
    {
        return match ($this) {
            $this::PENDING => 'gray',
            $this::APPROVED => 'primary',
            $this::RELEASED,
            $this::EXTENDED => 'info',
            $this::CANCEL => 'danger',
            $this::REJECTED => 'danger',
            $this::RETURNED => 'success'
        };
    }
}
