<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\MatchRepositoryInterface;
use App\Repositories\Contracts\CuratedMatchRepositoryInterface;
use App\Repositories\Contracts\CuratedMatchesWindowRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\CuratedMatchServiceInterface;

/**
 * Batch-oriented curated matching service for weekly windows.
 */
class CuratedMatchService extends BaseService implements CuratedMatchServiceInterface
{
    protected CuratedMatchRepositoryInterface $curatedMatchRepository;
    protected CuratedMatchesWindowRepositoryInterface $curatedMatchesWindowRepository;
    protected MatchRepositoryInterface $matchRepository;
    protected UserProfileRepositoryInterface $userProfileRepository;

    public function __construct(
        CuratedMatchRepositoryInterface $curatedMatchRepository,
        CuratedMatchesWindowRepositoryInterface $curatedMatchesWindowRepository,
        MatchRepositoryInterface $matchRepository,
        UserProfileRepositoryInterface $userProfileRepository
    ) {
        parent::__construct($curatedMatchRepository);

        $this->curatedMatchRepository = $curatedMatchRepository;
        $this->curatedMatchesWindowRepository = $curatedMatchesWindowRepository;
        $this->matchRepository = $matchRepository;
        $this->userProfileRepository = $userProfileRepository;
    }

    public function expireActiveWindowsForPeriod(CarbonImmutable $periodStart): int
    {
        return $this->curatedMatchesWindowRepository->expireActiveWindowsBefore($periodStart);
    }

    public function generateWeeklyCuratedMatchesForUser(
        int $userId,
        CarbonImmutable $windowStart,
        int $maxItems = 5,
        int $maxDistanceKm = 50
    ): void {
        $coordinates = $this->userProfileRepository->getCoordinatesForUser($userId);
        if ($coordinates === null) {
            return;
        }

        $windowEnd = $windowStart->addWeek();
        $now = now();

        DB::transaction(function () use ($userId, $coordinates, $windowStart, $windowEnd, $maxItems, $maxDistanceKm, $now) {
            $window = $this->curatedMatchesWindowRepository->firstOrCreateForUserPeriod(
                $userId,
                $windowStart,
                $windowEnd,
                $maxItems
            );

            $potentialMatches = $this->matchRepository->getPotentialMatches(
                $userId,
                (float) $coordinates['latitude'],
                (float) $coordinates['longitude'],
                $maxDistanceKm,
                $maxItems
            );

            $this->curatedMatchRepository->deleteForWindow((int) $window->id);

            if (count($potentialMatches) === 0) {
                return;
            }

            $rows = [];
            foreach ($potentialMatches as $match) {
                if (!isset($match->user_id, $match->match_score)) {
                    continue;
                }

                $rows[] = [
                    'curated_matches_window_id' => (int) $window->id,
                    'user_id' => (int) $match->user_id,
                    'rank_score' => (float) $match->match_score,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $this->curatedMatchRepository->insertRows($rows);
        });
    }

    public function fetchActiveCuratedMatchesForUser(int $userId): Collection
    {
        $window = $this->curatedMatchesWindowRepository->findActiveForUser($userId);

        if ($window === null) {
            return collect([]);
        }

        return $this->curatedMatchRepository->fetchForActiveWindowUserId((int) $window->id);
    }
}
