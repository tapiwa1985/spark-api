<?php

namespace App\Services;

use App\Services\Contracts\InterestCategoryServiceInterface;
use App\Repositories\Contracts\InterestCategoryRepositoryInterface;

/**
 * Read-side service for {@see \App\Models\InterestCategory}; inherits generic listing from {@see BaseService}.
 */
class InterestCategoryService extends BaseService implements InterestCategoryServiceInterface
{
    /**
     * @param InterestCategoryRepositoryInterface $repository Repository bound to the category aggregate.
     */
    public function __construct(InterestCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
