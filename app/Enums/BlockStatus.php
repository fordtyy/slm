<?php

namespace App\Enums;

enum BlockStatus: string
{
    case BLOCKED = 'Blocked';
    case UNBLOCKED = 'Unblocked';
}
