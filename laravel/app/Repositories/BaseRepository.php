<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * The model instance
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Create a new repository instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get the model instance
     *
     * @return Model
     */
    abstract public function getModel(): Model;

    /**
     * Get all records
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Get record by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        return $record ? $record->update($data) : false;
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);
        return $record ? $record->delete() : false;
    }

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Find records by field
     *
     * @param string $field
     * @param mixed $value
     * @return Collection
     */
    public function findBy(string $field, $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }
}
