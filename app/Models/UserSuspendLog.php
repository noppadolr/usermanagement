<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserSuspendAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserSuspendLog extends Model
{
    protected $fillable = [
        'user_id',
        'actor_id',
        'action',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'action' => UserSuspendAction::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
