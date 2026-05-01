<?php

namespace App\Services;

use App\Services\Contracts\InterestCategoryServiceInterface;
use App\Repositories\Contracts\InterestCategoryRepositoryInterface;

/**
 * Class InterestCategoryService
 *
 * @package App\Services
 */
class InterestCategoryService extends BaseService implements InterestCategoryServiceInterface
{
    /**
     * @param InterestCategoryRepositoryInterface $repository
     */
    public function __construct(InterestCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
