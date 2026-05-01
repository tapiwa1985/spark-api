<?php

namespace App\Repositories;

use App\Models\InterestCategory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InterestCategoryRepository
 *
 * @package App\Repositories
 */
class InterestCategoryRepository extends BaseRepository implements Contracts\InterestCategoryRepositoryInterface
{
    /**
     * @var Model $model
     */
    protected Model $model;

    /**
     * InterestCategoryRepository constructor.
     *
     * @param InterestCategory $model
     */
    public function __construct(InterestCategory $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }
}
