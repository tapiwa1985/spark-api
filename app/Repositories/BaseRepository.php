<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\BaseRepositoryInterface;

/**
 * Generic Eloquent-backed persistence helpers shared by concrete repositories.
 */
class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    /**
     * @param Model $model Root model class this repository operates on.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException When no row matches primary key.
     */
    public function find(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @return Model Fresh model instance after insert.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @return Model|null Updated model when found; otherwise null after {@see find} fails internally.
     */
    public function update(int $id, array $data): ?Model
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }

        return null;
    }

    /**
     * @return bool Whether Eloquent reported a successful delete.
     */
    public function delete(int $id)
    {
        $record = $this->find($id);
        if ($record) {
            return $record->delete();
        }
        return false;
    }
}
