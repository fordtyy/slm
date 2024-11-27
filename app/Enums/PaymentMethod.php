<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case IN_COUNTER = 'In Counter';
    case GCASH = 'Gcash';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
