<?php

namespace App\Services;

use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Create a new service instance.
     *
     */
    public function __construct(
        //
    ) {}

    /**
     * Get paginated users with filtering and sorting
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(array $filters): LengthAwarePaginator
    {
        $perPage = $this->sanitizePerPage($filters['per_page'] ?? 15);
        $search = $filters['search'] ?? '';
        $teamId = $filters['team_id'] ?? null;
        $roleIds = $filters['role_ids'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'asc';
        $query = User::with(['team', 'roles', 'profile']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($teamId)) {
            $query->where('team_id', $teamId);
        }

        if (!empty($roleIds)) {
            $roleIds = is_array($roleIds) ? $roleIds : [$roleIds];
            $query->whereHas('roles', function ($q) use ($roleIds) {
                $q->whereIn('roles.id', $roleIds);
            });
        }
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage);
    }

    private function sanitizePerPage($perPage): int
    {
        $perPage = (int) $perPage;
        return max(1, min($perPage, 100));
    }

    /**
     * Get single user with related data
     *
     * @param int $userId
     * @return User
     */
    public function getUserDetails(int $userId): User
    {
        $user = User::findOrFail($userId);
        return $user->load(['team', 'roles', 'profile', 'userRoles']);
    }

    /**
     * Create user with profile and role assignments
     *
     * @param array $data
     * @return User
     */
    public function createUserWithRelations(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'team_id' => $data['team_id'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        $user->profile()->create($data['profile'] ?? []);

        if (!empty($data['roles'])) {
            foreach ((array) $data['roles'] as $roleId) {
                $user->userRoles()->create([
                    'role_id' => $roleId,
                    'team_id' => $data['team_id'],
                ]);
            }
        }

        SendWelcomeEmailJob::dispatch($user, $data['password']);

        return $user->load(['team', 'roles', 'profile']);
    }

    /**
     * Update user, profile, and roles
     *
     * @param int $userId
     * @param array $data
     * @return User
     */
    public function updateUserWithRelations(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        if (isset($data['team_id'])) {
            $updateData['team_id'] = $data['team_id'];
        }
        if (isset($data['is_active'])) {
            $updateData['is_active'] = $data['is_active'];
        }
        if (isset($data['password'])) {
            $updateData['password'] = bcrypt($data['password']);
        }
        
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        if (isset($data['profile'])) {
            $user->profile()->updateOrCreate([
                'user_id' => $user->id,
            ], $data['profile']);
        }

        if (isset($data['roles'])) {
            $user->userRoles()->delete();
            $teamId = $user->team_id;

            foreach ((array) $data['roles'] as $roleId) {
                $user->userRoles()->create([
                    'role_id' => $roleId,
                    'team_id' => $teamId,
                ]);
            }
        }

        return $user->load(['team', 'roles', 'profile']);
    }

    /**
     * Delete user
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        $user = User::findOrFail($userId);

        if ($user->roles()->where('slug', 'super-admin')->exists()) {
            throw new \RuntimeException('Cannot delete super admin user.');
        }

        return $user->delete();
    }

    /**
     * Restore soft deleted user
     *
     * @param int $userId
     * @return User
     */
    public function restoreUser(int $userId): User
    {
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();

        return $user->load(['team', 'roles', 'profile']);
    }

    /**
     * Permanently delete user
     *
     * @param int $userId
     * @return bool|null
     */
    public function forceDeleteUser(int $userId)
    {
        $user = User::withTrashed()->findOrFail($userId);

        return $user->forceDelete();
    }

    /**
     * Activate user
     *
     * @param int $userId
     * @return User
     */
    public function activateUser(int $userId): User
    {
        $user = User::findOrFail($userId);

        $user->is_active = true;
        $user->save();

        return $user;
    }

    /**
     * Deactivate user
     *
     * @param int $userId
     * @return User
     */
    public function deactivateUser(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->is_active = false;
        $user->save();

        return $user;
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get all users in a team
     *
     * @param int $teamId
     * @return mixed
     */
    public function getUsersByTeam(int $teamId)
    {
        return User::where('team_id', $teamId)->get();
    }

    /**
     * Get users with specific roles
     *
     * @param int|array $roleIds
     * @return mixed
     */
    public function getUsersByRole($roleIds)
    {
        return User::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('id', (array) $roleIds);
        })->get();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return mixed
     */
    public function createUser(array $data)
    {
        return User::create($data);
    }

    /**
     * Update user
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateUser(int $userId, array $data): bool
    {
        $user = User::findOrFail($userId);

        return $user->update($data);
    }

    /**
     * Get user by ID
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserById(int $userId)
    {
        return User::findOrFail($userId);
    }

    /**
     * Get all users
     *
     * @return mixed
     */
    public function getAllUsers()
    {
        return User::all();
    }

    /**
     * Get paginated users without filters
     *
     * @param int $perPage
     * @return mixed
     */
    public function getPaginatedUsersSimple(int $perPage = 15)
    {
        return User::paginate($perPage);
    }
}
