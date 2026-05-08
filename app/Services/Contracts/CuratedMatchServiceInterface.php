<?php

namespace App\Services\Contracts;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

interface CuratedMatchServiceInterface extends BaseServiceInterface
{
    /**
     * Expire all previous active windows before a new period starts.
     */
    public function expireActiveWindowsForPeriod(CarbonImmutable $periodStart): int;

    /**
     * Generate weekly curated matches for a single user.
     */
    public function generateWeeklyCuratedMatchesForUser(
        int $userId,
        CarbonImmutable $windowStart,
        int $maxItems = 5,
        int $maxDistanceKm = 50
    ): void;

    /**
     * Fetch active-window curated matches for a user.
     *
     * @return Collection<int, \App\Models\CuratedMatch>
     */
    public function fetchActiveCuratedMatchesForUser(int $userId): Collection;
}
