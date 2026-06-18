<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class WorkGroup extends Model
{
    protected $fillable = [
        'mission_group_id',
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function missionGroup(): BelongsTo
    {
        return $this->belongsTo(MissionGroup::class);
    }

    public function workUnits(): HasMany
    {
        return $this->hasMany(WorkUnit::class);
    }
}
