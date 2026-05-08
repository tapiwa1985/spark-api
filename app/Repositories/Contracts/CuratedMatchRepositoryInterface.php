<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface CuratedMatchRepositoryInterface
 *
 * Defines the contract for curated match repository operations.
 * Extends the base repository interface to inherit common database operations.
 *
 * @package App\Repositories\Contracts
 */
interface CuratedMatchRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteForWindow(int $windowId): int;

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function insertRows(array $rows): void;

    /**
     * @return Collection<int, \App\Models\CuratedMatch>
     */
    public function fetchForActiveWindowUserId(int $activeWindowId): Collection;
}
