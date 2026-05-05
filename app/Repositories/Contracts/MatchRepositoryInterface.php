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
     * Retrieve active mutual matches as the other party’s profiles.
     *
     * Each {@see \App\Models\UserProfile} has `user_match_id` set to the matching
     * {@see \App\Models\UserMatch} primary key when returned from {@see MatchRepository::getMatchesForUser}.
     *
     * @param int $userId The ID of the authenticated user.
     * @return Collection<int, \App\Models\UserProfile>
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
