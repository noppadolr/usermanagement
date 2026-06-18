<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class WorkUnit extends Model
{
    protected $fillable = [
        'work_group_id',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function workGroup(): BelongsTo
    {
        return $this->belongsTo(WorkGroup::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
