<?php

declare(strict_types=1);

namespace App\DTOs\Users;

final readonly class PasswordData
{
    public function __construct(
        public string $password,
        public ?string $adminKeyPass = null,
    ) {
    }
}
