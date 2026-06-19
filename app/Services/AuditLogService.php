<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

final class AuditLogService
{
    public function record(
        ?User $actor,
        Model $auditable,
        string $action,
        ?array $oldValues,
        ?array $newValues,
        ?Request $request = null,
    ): void {
        AuditLog::query()->create([
            'actor_id' => $actor?->id,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
