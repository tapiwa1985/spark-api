<?php

namespace App\Services;

use App\Services\Contracts\LikeServiceInterface;
use App\Repositories\Contracts\LikeRepositoryInterface;

class LikeService extends BaseService implements LikeServiceInterface
{
    public function __construct(LikeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
