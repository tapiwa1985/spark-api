<?php

namespace App\Services;

use App\Repositories\Contracts\IndustryRepositoryInterface;
use App\Services\Contracts\IndustryServiceInterface;

class IndustryService extends BaseService implements IndustryServiceInterface
{
    /**
     * @var IndustryRepositoryInterface
     */
    protected IndustryRepositoryInterface $repo;

    /**
     * IndustryService Constructor.
     *
     * @param IndustryRepositoryInterface
     */
    public function __construct(IndustryRepositoryInterface $repo)
    {
        parent::__construct($repo);
    }
}
