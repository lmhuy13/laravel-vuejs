<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if user can view any users
     */
    public function viewAny(User $user): bool
    {
        return $user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Determine if user can view user
     */
    public function view(User $user, User $model): bool
    {
        // Admin can view any user
        if ($user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists()) {
            return true;
        }

        // User can view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if user can create user
     */
    public function create(User $user): bool
    {
        return $user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Determine if user can update user
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update any user
        if ($user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists()) {
            return true;
        }

        // User can only update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine if user can delete user
     */
    public function delete(User $user, User $model): bool
    {
        // Super admin can delete any user except themselves
        if ($user->roles()->where('slug', 'super-admin')->exists()) {
            return $user->id !== $model->id;
        }

        // Admin cannot delete super-admin users
        if ($model->roles()->where('slug', 'super-admin')->exists()) {
            return false;
        }

        return $user->roles()->where('slug', 'admin')->exists();
    }

    /**
     * Determine if user can restore user
     */
    public function restore(User $user, User $model): bool
    {
        return $user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Determine if user can permanently delete user
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->roles()->where('slug', 'super-admin')->exists();
    }
}
