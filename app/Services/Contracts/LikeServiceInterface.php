<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

interface LikeServiceInterface
{
    public function create(array $data): Model;

    /**
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getReceivedLikes(int $userId): Collection;
}
