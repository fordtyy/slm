<?php

namespace App\Enums;

enum PenaltyStatus: string
{
    case PENDING = 'Pending';
    case ON_PROCESS = 'On Process';
    case RESOLVE = 'Resolved';
}
