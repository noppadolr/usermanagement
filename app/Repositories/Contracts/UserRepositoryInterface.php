<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function paginateByStatus(UserStatus|array $status, ?string $search, int $perPage = 10): LengthAwarePaginator;

    public function findWithRelations(int $id): User;

    public function create(array $payload): User;

    public function update(User $user, array $payload): User;

    public function delete(User $user): void;

    public function activeManagersAndUsers(): Collection;
}
