<?php

//
// namespace App\Policies;
//
// use App\Models\User;
// use Illuminate\Auth\Access\Response;
//
// class UserPolicy
// {
//    /**
//     * Determine whether the user can view any models.
//     */
//    public function viewAny(User $user): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can view the model.
//     */
//    public function view(User $user, User $model): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can create models.
//     */
//    public function create(User $user): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can update the model.
//     */
//    public function update(User $user, User $model): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can delete the model.
//     */
//    public function delete(User $user, User $model): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can restore the model.
//     */
//    public function restore(User $user, User $model): bool
//    {
//        return false;
//    }
//
//    /**
//     * Determine whether the user can permanently delete the model.
//     */
//    public function forceDelete(User $user, User $model): bool
//    {
//        return false;
//    }
// }

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return in_array($actor->role, [
            UserRole::Admin,
            UserRole::Manager,
        ], true);
    }

    public function view(User $actor, User $user): bool
    {
        return $actor->isAdmin()
            || $actor->isManager()
            || $actor->id === $user->id;
    }

    public function create(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function update(User $actor, User $user): bool
    {
        return $actor->isAdmin()
            || $actor->isManager()
            || $actor->id === $user->id;
    }

    public function updateRole(User $actor, User $user): bool
    {
        return $actor->isAdmin();
    }

    public function updatePassword(User $actor, User $user): bool
    {
        return $actor->isAdmin()
            || $actor->id === $user->id;
    }

    public function resetPassword(User $actor, User $user): bool
    {
        return $actor->isAdmin();
    }

    public function suspend(User $actor, User $user): bool
    {
        return $actor->isAdmin()
            && $actor->id !== $user->id;
    }

    public function unsuspend(User $actor, User $user): bool
    {
        return $actor->isAdmin()
            && $actor->id !== $user->id;
    }

    public function delete(User $actor, User $user): bool
    {
        return $actor->isAdmin()
            && $actor->id !== $user->id;
    }
}
