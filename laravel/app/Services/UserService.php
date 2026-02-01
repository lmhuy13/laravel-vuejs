<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Create a new service instance.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get paginated users with filtering and sorting
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(array $filters): LengthAwarePaginator
    {
        return $this->userRepository->getPaginatedWithFilters($filters);
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get all users in a team
     *
     * @param int $teamId
     * @return mixed
     */
    public function getUsersByTeam(int $teamId)
    {
        return $this->userRepository->getByTeam($teamId);
    }

    /**
     * Get users with specific roles
     *
     * @param int|array $roleIds
     * @return mixed
     */
    public function getUsersByRole($roleIds)
    {
        return $this->userRepository->getByRole($roleIds);
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return mixed
     */
    public function createUser(array $data)
    {
        return $this->userRepository->create($data);
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
        return $this->userRepository->update($userId, $data);
    }

    /**
     * Delete user
     *
     * @param int $userId
     * @return bool
     */
    public function deleteUser(int $userId): bool
    {
        return $this->userRepository->delete($userId);
    }

    /**
     * Get user by ID
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserById(int $userId)
    {
        return $this->userRepository->find($userId);
    }

    /**
     * Get all users
     *
     * @return mixed
     */
    public function getAllUsers()
    {
        return $this->userRepository->all();
    }

    /**
     * Get paginated users without filters
     *
     * @param int $perPage
     * @return mixed
     */
    public function getPaginatedUsersSimple(int $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }
}
