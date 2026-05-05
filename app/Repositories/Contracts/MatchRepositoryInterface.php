<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Repository interface for match-related database operations.
 *
 * Defines methods for retrieving active matches and performing unmatch actions.
 * Extends the base repository interface to inherit common CRUD operations.
 *
 * @package App\Repositories\Contracts
 */
interface MatchRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Retrieve all active matches for a given user.
     *
     * Returns a collection of match records where the given user is either the `user_id`
     * or the `matched_user_id`, and the match status is active (e.g., `'ACTIVE'`).
     *
     * @param int $userId The ID of the authenticated user.
     * @return Collection Collection of {@see \App\Models\UserMatch} models representing active matches.
     */
    public function getMatchesForUser(int $userId): Collection;

    /**
     * Soft‑delete a specific match (unmatch) by a user.
     *
     * This method should ensure that only the user performing the unmatch has their side
     * of the match removed or that the match record is soft‑deleted (depending on business logic).
     * Typically called after authorization checks have been performed.
     *
     * @param int $unmatchedByUserId The ID of the user initiating the unmatch.
     * @param int $userMatchId The primary key of the match record to delete.
     * @return void
     */
    public function unmatch(int $unmatchedByUserId, int $userMatchId): void;
}
