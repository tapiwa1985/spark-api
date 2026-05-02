<?php

namespace App\Services;

use App\Services\Contracts\BaseServiceInterface;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Thin orchestration layer over a {@see BaseRepositoryInterface} for generic CRUD delegating to Eloquent.
 */
class BaseService implements BaseServiceInterface
{
    /**
     * Primary persistence abstraction for this service (typically one aggregate root).
     */
    protected BaseRepositoryInterface $repository;

    /**
     * @param BaseRepositoryInterface $repository Concrete repository injected by subtype constructors.
     */
    public function __construct(BaseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Model Newly persisted model instance from the backing repository.
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * @return Collection<int, Model> All rows for the bound model type.
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * @return Model|null Single row when present; repositories may throw instead of returning null.
     */
    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * @return Model|null Updated model when the underlying row existed.
     */
    public function update(int $id, array $data): ?Model
    {
        return $this->repository->update($id, $data);
    }

    /**
     * @return bool Whether the repository reported a successful delete.
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
