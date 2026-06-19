<?php

declare(strict_types=1);

namespace App\DTOs\Users;

use App\Enums\UserRole;
use App\Enums\UserStatus;

final readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public UserRole $role,
        public UserStatus $status,
        public ?int $positionId,
        public ?int $workUnitId,
        public ?string $password = null,
        public ?string $keyPass = null,
    ) {
    }
}
