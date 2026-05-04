<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface MatchRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getMatchesForUser(int $userId): Collection;
}
