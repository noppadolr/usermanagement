<?php

declare(strict_types=1);

namespace App\DTOs\Users;

final readonly class SuspendUserData
{
    public function __construct(
        public string $reason,
        public string $adminKeyPass,
    ) {
    }
}
