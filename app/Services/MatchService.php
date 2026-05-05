<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Services\Contracts\MatchServiceInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;

/**
 * Orchestrates user match persistence and resolves matched users to {@see \App\Models\UserProfile} rows.
 *
 * @package App\Services
 */
class MatchService extends BaseService implements MatchServiceInterface
{
    /**
     * Typed alias of {@see BaseService::$repository} as {@see MatchRepositoryInterface}.
     *
     * @var MatchRepositoryInterface
     */
    protected MatchRepositoryInterface $matchRepository;

    /**
     * Inject the match repository dependency.
     *
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

    /**
     * Perform an unmatch (soft‑delete) of a match record.
     *
     * Delegates the operation to the match repository, which handles the transaction,
     * status update, and soft deletion. Authorization is expected to be verified
     * before calling this method.
     *
     * @param int $unmatchedByUserId The ID of the user initiating the unmatch.
     * @param int $userMatchId The primary key of the match to unmatch.
     * @return void
     */
    public function unmatch(int $unmatchedByUserId, int $userMatchId): void
    {
        $this->matchRepository->unmatch($unmatchedByUserId, $userMatchId);
    }
}
