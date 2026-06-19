<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\UserStatus;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository implements UserRepositoryInterface
{
    public function paginateByStatus(UserStatus|array $status, ?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $statuses = is_array($status)
            ? array_map(static fn (UserStatus $item): string => $item->value, $status)
            : [$status->value];

        return User::query()
            ->with([
                'position',
                'workUnit.workGroup.missionGroup',
            ])
            ->whereIn('status', $statuses)
            ->when($search, function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): User
    {
        return User::query()
            ->with([
                'position',
                'workUnit.workGroup.missionGroup',
                'suspendLogs.actor',
            ])
            ->findOrFail($id);
    }

    public function create(array $payload): User
    {
        return User::query()->create($payload);
    }

    public function update(User $user, array $payload): User
    {
        $user->update($payload);

        return $user->refresh()->load([
            'position',
            'workUnit.workGroup.missionGroup',
        ]);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function activeManagersAndUsers(): Collection
    {
        return User::query()
            ->with([
                'position',
                'workUnit.workGroup.missionGroup',
            ])
            ->where('status', UserStatus::Active)
            ->get();
    }
}
