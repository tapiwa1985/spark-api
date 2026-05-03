<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface LikeServiceInterface extends BaseServiceInterface
{
    /**
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getReceivedLikes(int $userId): Collection;
}
