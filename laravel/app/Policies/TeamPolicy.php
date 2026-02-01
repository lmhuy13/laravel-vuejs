<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Determine if user can view any teams
     */
    public function viewAny(User $user): bool
    {
        return $user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Determine if user can view team
     */
    public function view(User $user, Team $team): bool
    {
        // Admin can view any team
        if ($user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists()) {
            return true;
        }

        // User can view their own team
        return $user->team_id === $team->id;
    }

    /**
     * Determine if user can create team
     */
    public function create(User $user): bool
    {
        return $user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Determine if user can update team
     */
    public function update(User $user, Team $team): bool
    {
        return $user->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Determine if user can delete team
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->roles()->whereIn('slug', ['super-admin'])->exists();
    }

    /**
     * Determine if user can restore team
     */
    public function restore(User $user, Team $team): bool
    {
        return $user->roles()->where('slug', 'super-admin')->exists();
    }

    /**
     * Determine if user can permanently delete team
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return $user->roles()->where('slug', 'super-admin')->exists();
    }
}
