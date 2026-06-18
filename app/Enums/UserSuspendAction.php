<?php

declare(strict_types=1);

namespace App\Enums;

enum UserSuspendAction: string
{
    case Suspend = 'suspend';
    case Unsuspend = 'unsuspend';
}
