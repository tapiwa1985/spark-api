<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

/**
 * Service interface for match-related business logic.
 *
 * Defines operations for retrieving active matches for a user and
 * handling the unmatch (removal) of a match. Extends the base service interface
 * to inherit common service methods.
 *
 * @package App\Services\Contracts
 */
interface MatchServiceInterface extends BaseServiceInterface
{
    /**
     * Fetch all active matches for a given user as a collection of user profiles.
     *
     * This method should return the profiles of users that the given user
     * is currently matched with (status ACTIVE). The match can be in either direction
     * (user as initiator or as the matched party).
     *
     * @param int $userId The ID of the authenticated user.
     * @return Collection Collection of {@see \App\Models\UserProfile} models representing matched users.
     */
    public function fetchMatchesForUser(int $userId): Collection;

    /**
     * Unmatch (soft‑delete) a specific match.
     *
     * Performs the business logic to remove a match, including updating the match status,
     * recording who initiated the unmatch, and triggering any side effects (e.g., soft deletion,
     * event dispatching). Authorization is assumed to have been checked before calling this method.
     *
     * @param int $unmatchedByUserId The ID of the user performing the unmatch.
     * @param int $userMatchId The primary key of the match to unmatch.
     * @return void
     */
    public function unmatch(int $unmatchedByUserId, int $userMatchId): void;
}
