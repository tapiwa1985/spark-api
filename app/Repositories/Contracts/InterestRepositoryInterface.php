<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface InterestRepositoryInterface
 * @package App\Repositories\Contracts
 */
interface InterestRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find interests by category ID.
      *
      * @param int $categoryId
      * @return Collection
     */
    public function findByCategoryId(int $categoryId): Collection;
}
