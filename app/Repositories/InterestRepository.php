<?php

namespace App\Repositories;

use App\Models\Interest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\InterestRepositoryInterface;

/**
 * Class InterestRepository
 * @package App\Repositories
 */
class InterestRepository extends BaseRepository implements InterestRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * InterestRepository constructor.
     *
     * @param Interest
     */
    public function __construct(Interest $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $categoryId
     * @return Collection
     */
    public function findByCategoryId(int $categoryId): Collection
    {
        return $this->model->where('interest_category_id', $categoryId)->get();
    }
}
