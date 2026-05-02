<?php

namespace App\Repositories;

use App\Models\Interest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\InterestRepositoryInterface;

/**
 * {@see Interest} persistence including lookup scoped by category.
 */
class InterestRepository extends BaseRepository implements InterestRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param Interest $model Interest tag model.
     */
    public function __construct(Interest $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection<int, \App\Models\Interest>
     */
    public function findByCategoryId(int $categoryId): Collection
    {
        return $this->model->where('interest_category_id', $categoryId)->get();
    }
}
