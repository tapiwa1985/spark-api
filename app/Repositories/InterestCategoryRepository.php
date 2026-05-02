<?php

namespace App\Repositories;

use App\Models\InterestCategory;
use Illuminate\Database\Eloquent\Model;

/**
 * {@see InterestCategory} listing through generic {@see BaseRepository} behavior.
 */
class InterestCategoryRepository extends BaseRepository implements Contracts\InterestCategoryRepositoryInterface
{
    /**
     * @var Model $model
     */
    protected Model $model;

    /**
     * @param InterestCategory $model Category aggregate root.
     */
    public function __construct(InterestCategory $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }
}
