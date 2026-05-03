<?php

namespace App\Services;

use App\Services\Contracts\MatchServiceInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;

class MatchService extends BaseService implements MatchServiceInterface
{
    public function __construct(MatchRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
