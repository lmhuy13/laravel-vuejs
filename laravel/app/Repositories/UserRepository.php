<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Get the model instance
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return new User();
    }

    /**
     * Get paginated users with filters and sorting
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedWithFilters(array $filters): LengthAwarePaginator
    {
        $perPage = $this->sanitizePerPage($filters['per_page'] ?? 15);
        $search = $filters['search'] ?? '';
        $teamId = $filters['team_id'] ?? null;
        $roleIds = $filters['role_ids'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'asc';

        $query = $this->model->with(['team', 'roles', 'profile']);

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

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get users by team
     *
     * @param int $teamId
     * @return mixed
     */
    public function getByTeam(int $teamId)
    {
        return $this->model->where('team_id', $teamId)->get();
    }

    /**
     * Get users by role
     *
     * @param int|array $roleIds
     * @return mixed
     */
    public function getByRole($roleIds)
    {
        $roleIds = is_array($roleIds) ? $roleIds : [$roleIds];
        return $this->model->whereHas('roles', function ($q) use ($roleIds) {
            $q->whereIn('roles.id', $roleIds);
        })->get();
    }

    /**
     * Sanitize per_page parameter
     *
     * @param mixed $perPage
     * @return int
     */
    private function sanitizePerPage($perPage): int
    {
        $perPage = (int) $perPage;
        return max(1, min($perPage, 100));
    }
}
