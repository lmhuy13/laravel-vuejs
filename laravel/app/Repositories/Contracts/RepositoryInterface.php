<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface RepositoryInterface
{
    /**
     * Get all records
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Get record by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 15);

    /**
     * Find records by field
     *
     * @param string $field
     * @param mixed $value
     * @return Collection
     */
    public function findBy(string $field, $value): Collection;
}
