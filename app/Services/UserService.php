<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Users\PasswordData;
use App\DTOs\Users\SuspendUserData;
use App\DTOs\Users\UserData;
use App\Enums\UserStatus;
use App\Enums\UserSuspendAction;
use App\Models\User;
use App\Models\UserSuspendLog;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function create(UserData $data, User $actor, Request $request): User
    {
        return DB::transaction(function () use ($data, $actor, $request): User {
            $user = $this->users->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password ?? '111',
                'role' => $data->role,
                'status' => $data->status,
                'position_id' => $data->positionId,
                'work_unit_id' => $data->workUnitId,
                'key_pass_hash' => $data->keyPass ? Hash::make($data->keyPass) : null,
            ]);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $user,
                action: 'created',
                oldValues: null,
                newValues: $user->only([
                    'name',
                    'email',
                    'role',
                    'status',
                    'position_id',
                    'work_unit_id',
                ]),
                request: $request,
            );

            return $user;
        });
    }

    public function update(User $user, UserData $data, User $actor, Request $request): User
    {
        return DB::transaction(function () use ($user, $data, $actor, $request): User {
            $oldValues = $user->only([
                'name',
                'email',
                'role',
                'status',
                'position_id',
                'work_unit_id',
            ]);

            $payload = [
                'name' => $data->name,
                'email' => $data->email,
                'status' => $data->status,
                'position_id' => $data->positionId,
                'work_unit_id' => $data->workUnitId,
            ];

            if ($actor->can('updateRole', $user)) {
                $payload['role'] = $data->role;
            }

            if ($data->keyPass !== null && $data->keyPass !== '') {
                $payload['key_pass_hash'] = Hash::make($data->keyPass);
            }

            $updated = $this->users->update($user, $payload);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $updated,
                action: 'updated',
                oldValues: $oldValues,
                newValues: $updated->only([
                    'name',
                    'email',
                    'role',
                    'status',
                    'position_id',
                    'work_unit_id',
                ]),
                request: $request,
            );

            return $updated;
        });
    }

    public function delete(User $user, User $actor, Request $request, string $adminKeyPass): void
    {
        DB::transaction(function () use ($user, $actor, $request, $adminKeyPass): void {
            $this->ensureValidKeyPass($actor, $adminKeyPass);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $user,
                action: 'deleted',
                oldValues: $user->only(['name', 'email', 'role', 'status']),
                newValues: null,
                request: $request,
            );

            $this->users->delete($user);
        });
    }

    public function updatePassword(User $user, PasswordData $data, User $actor, Request $request): void
    {
        DB::transaction(function () use ($user, $data, $actor, $request): void {
            if ($actor->isAdmin() && $actor->id !== $user->id) {
                $this->ensureValidKeyPass($actor, (string) $data->adminKeyPass);
            }

            $this->users->update($user, [
                'password' => $data->password,
            ]);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $user,
                action: 'password_updated',
                oldValues: null,
                newValues: ['password' => 'changed'],
                request: $request,
            );
        });
    }

    public function resetPasswordToDefault(User $user, User $actor, Request $request, string $adminKeyPass): void
    {
        DB::transaction(function () use ($user, $actor, $request, $adminKeyPass): void {
            $this->ensureValidKeyPass($actor, $adminKeyPass);

            $this->users->update($user, [
                'password' => '111',
            ]);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $user,
                action: 'password_reset_to_default',
                oldValues: null,
                newValues: ['password' => 'reset_to_111'],
                request: $request,
            );
        });
    }

    public function suspend(User $user, SuspendUserData $data, User $actor, Request $request): void
    {
        DB::transaction(function () use ($user, $data, $actor, $request): void {
            $this->ensureValidKeyPass($actor, $data->adminKeyPass);

            $oldValues = $user->only(['status']);

            $this->users->update($user, [
                'status' => UserStatus::Suspended,
            ]);

            UserSuspendLog::query()->create([
                'user_id' => $user->id,
                'actor_id' => $actor->id,
                'action' => UserSuspendAction::Suspend,
                'reason' => $data->reason,
            ]);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $user,
                action: 'suspended',
                oldValues: $oldValues,
                newValues: ['status' => UserStatus::Suspended->value, 'reason' => $data->reason],
                request: $request,
            );
        });
    }

    public function unsuspend(User $user, SuspendUserData $data, User $actor, Request $request): void
    {
        DB::transaction(function () use ($user, $data, $actor, $request): void {
            $this->ensureValidKeyPass($actor, $data->adminKeyPass);

            $oldValues = $user->only(['status']);

            $this->users->update($user, [
                'status' => UserStatus::Active,
            ]);

            UserSuspendLog::query()->create([
                'user_id' => $user->id,
                'actor_id' => $actor->id,
                'action' => UserSuspendAction::Unsuspend,
                'reason' => $data->reason,
            ]);

            $this->auditLogService->record(
                actor: $actor,
                auditable: $user,
                action: 'unsuspended',
                oldValues: $oldValues,
                newValues: ['status' => UserStatus::Active->value, 'reason' => $data->reason],
                request: $request,
            );
        });
    }

    private function ensureValidKeyPass(User $actor, string $keyPass): void
    {
        if ($actor->key_pass_hash === null || ! Hash::check($keyPass, $actor->key_pass_hash)) {
            throw ValidationException::withMessages([
                'adminKeyPass' => 'Key pass ไม่ถูกต้อง',
            ]);
        }
    }
}
