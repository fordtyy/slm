<?php

namespace App\Enums;

enum BorrowStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case RETURNED = 'RETURNED';
}
