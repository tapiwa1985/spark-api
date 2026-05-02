<?php

namespace App\Services;

use App\Repositories\Contracts\IndustryRepositoryInterface;
use App\Services\Contracts\IndustryServiceInterface;

/**
 * Lists {@see \App\Models\Industry} records through {@see IndustryRepositoryInterface}.
 */
class IndustryService extends BaseService implements IndustryServiceInterface
{
    /**
     * Same repository reference as {@see BaseService::$repository}, retained for explicit typing in this service.
     */
    protected IndustryRepositoryInterface $repo;

    /**
     * @param IndustryRepositoryInterface $repo Industry aggregate repository.
     */
    public function __construct(IndustryRepositoryInterface $repo)
    {
        parent::__construct($repo);
    }
}
