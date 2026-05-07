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

    /**
     * Retrieves potential matches for a user based on location and discovery preferences.
     *
     * This method calls a database stored procedure/function `get_potential_matches_full`
     * that calculates and returns users who match the specified user's discovery criteria
     * within the given geographic radius.
     *
     * @param int $userId The unique identifier of the user to find matches for
     * @param float $lat The latitude coordinate of the user's current location
     * @param float $lng The longitude coordinate of the user's current location
     * @param int $maxDistanceKm Maximum distance in kilometers to search for potential matches (default: 50)
     * @param int $limit Maximum number of potential matches to return (default: 50)
     *
     * @return array An array of potential matches containing user profiles and match scores
     *
     * @throws \Illuminate\Database\QueryException If the database query execution fails
     */
    public function getPotentialMatches(int $userId, float $lat, float $lng, int $maxDistanceKm = 50, int $limit = 50): array;
}
