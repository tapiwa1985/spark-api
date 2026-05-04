<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Services\Contracts\MatchServiceInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;

/**
 * Orchestrates user match persistence and resolves matched users to {@see \App\Models\UserProfile} rows.
 */
class MatchService extends BaseService implements MatchServiceInterface
{
    /**
     * Typed alias of {@see BaseService::$repository} as {@see MatchRepositoryInterface}.
     */
    protected MatchRepositoryInterface $matchRepository;

    /**
     * @param MatchRepositoryInterface $repository Match aggregate repository for CRUD and match listings.
     */
    public function __construct(MatchRepositoryInterface $repository)
    {
        parent::__construct($repository);

        $this->matchRepository = $repository;
    }

    /**
     * Returns profile rows for every user matched with the given account.
     *
     * @param int $userId Authenticated or subject user identifier.
     * @return Collection<int, \App\Models\UserProfile>
     */
    public function fetchMatchesForUser(int $userId): Collection
    {
        return $this->matchRepository->getMatchesForUser($userId);
    }
}
