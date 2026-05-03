<?php

namespace App\Services;

use App\Services\Contracts\LikeServiceInterface;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\LikeRepositoryInterface;

class LikeService extends BaseService implements LikeServiceInterface
{
    /**
     * @var LikeRepositoryInterface
     */
    protected LikeRepositoryInterface $repo;

    /**
     * Like service constructor
     *
     * @param LikeRepositoryInterface
     */
    public function __construct(LikeRepositoryInterface $repository)
    {
        parent::__construct($repository);

        $this->repo = $repository;
    }

    /**
     * @param int
     * @return \Illuminate\Support\Collection
     */
    public function getReceivedLikes(int $userId): Collection
    {
        return $this->repo->getReceivedLikes($userId);
    }
}
