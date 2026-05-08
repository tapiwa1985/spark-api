<?php

namespace App\Repositories\Contracts;

use Carbon\CarbonImmutable;
use App\Models\CuratedMatchesWindow;

/**
 * Interface CuratedMatchesWindowRepositoryInterface
 *
 * Defines the contract for curated matches window repository operations.
 * Extends the base repository interface to inherit common database operations.
 *
 * @package App\Repositories\Contracts
 */
interface CuratedMatchesWindowRepositoryInterface extends BaseRepositoryInterface
{
    public function expireActiveWindowsBefore(CarbonImmutable $periodStart): int;

    public function findActiveForUser(int $userId): ?CuratedMatchesWindow;

    public function firstOrCreateForUserPeriod(
        int $userId,
        CarbonImmutable $windowStart,
        CarbonImmutable $windowEnd,
        int $maxItems
    ): CuratedMatchesWindow;
}
