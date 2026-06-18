<?php

//
// namespace App\Models;
//
// // use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Database\Factories\UserFactory;
// use Illuminate\Database\Eloquent\Attributes\Fillable;
// use Illuminate\Database\Eloquent\Attributes\Hidden;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Str;
// use Laravel\Fortify\Contracts\PasskeyUser;
// use Laravel\Fortify\PasskeyAuthenticatable;
//
// /**
// * @property int $id
// * @property string $name
// * @property string $email
// * @property Carbon|null $email_verified_at
// * @property string $password
// * @property string|null $two_factor_secret
// * @property string|null $two_factor_recovery_codes
// * @property Carbon|null $two_factor_confirmed_at
// * @property string|null $remember_token
// * @property Carbon|null $created_at
// * @property Carbon|null $updated_at
// */
// #[Fillable(['name', 'email', 'password'])]
// #[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
// class User extends Authenticatable implements PasskeyUser
// {
//    /** @use HasFactory<UserFactory> */
//    use HasFactory, Notifiable, PasskeyAuthenticatable;
//
//    /**
//     * Get the attributes that should be cast.
//     *
//     * @return array<string, string>
//     */
//    protected function casts(): array
//    {
//        return [
//            'email_verified_at' => 'datetime',
//            'password' => 'hashed',
//        ];
//    }
//
//    /**
//     * Get the user's initials
//     */
//    public function initials(): string
//    {
//        return Str::of($this->name)
//            ->explode(' ')
//            ->take(2)
//            ->map(fn ($word) => Str::substr($word, 0, 1))
//            ->implode('');
//    }
// }

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'position_id',
        'work_unit_id',
        'key_pass_hash',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'key_pass_hash',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'last_login_at' => 'datetime',
            'email_verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isManager(): bool
    {
        return $this->role === UserRole::Manager;
    }

    public function canLogin(): bool
    {
        return $this->status->canLogin();
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function suspendLogs(): HasMany
    {
        return $this->hasMany(UserSuspendLog::class);
    }
}
