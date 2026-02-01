<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Get paginated users with filters and sorting
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedWithFilters(array $filters): LengthAwarePaginator;

    /**
     * Find user by email
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email);

    /**
     * Get users by team
     *
     * @param int $teamId
     * @return mixed
     */
    public function getByTeam(int $teamId);

    /**
     * Get users by role
     *
     * @param int|array $roleIds
     * @return mixed
     */
    public function getByRole($roleIds);
}
