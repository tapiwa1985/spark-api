<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface BaseRepositoryInterface
 *
 * @package App\Repositories
 */
interface BaseRepositoryInterface
{
    /**
     * Get all records from the repository.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a record by its ID.
     * @param int $id
     * @return Model
     */
    public function find(int $id): Model;

    /**
     * Create a new record in the repository.
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record in the repository.
     * @param int $id
     * @param array $data
     * @return Model|null
     */
    public function update(int $id, array $data): ?Model;

    /**
     * Delete a record from the repository.
     * @param int $id
     * @return bool
     */
    public function delete(int $id);
}
