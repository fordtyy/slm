<?php

namespace App\Enums;

enum ExtensionStatus: string
{
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case CANCEL = 'Cancel';
    case REJECTED = 'Rejected';
}
