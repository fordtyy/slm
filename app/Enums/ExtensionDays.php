<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ExtensionDays: int implements HasLabel
{
    case ONE_DAY = 1;
    case TWO_DAYS = 2;
    case THREE_DAYS = 3;
    case FOUR_DAYS = 4;
    case FIVE_DAYS = 5;

    public function getLabel(): ?string
    {
        return match ($this) {
            $this::ONE_DAY => '1 Day',
            $this::TWO_DAYS => '2 Days',
            $this::THREE_DAYS => '3 Days',
            $this::FOUR_DAYS => '4 Days',
            $this::FIVE_DAYS => '5 Days',
        };
    }
}
