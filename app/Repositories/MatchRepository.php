<?php

namespace App\Repositories;

use App\Models\UserMatch;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\MatchRepositoryInterface;

/**
 * Repository implementation for match-related database operations.
 *
 * Handles retrieving active matches for a user and performing the unmatch (soft‑delete) action.
 * Extends the base repository to inherit common CRUD methods.
 *
 * @package App\Repositories
 */
class MatchRepository extends BaseRepository implements MatchRepositoryInterface
{
    /**
     * The match model instance.
     *
     * @var Model|UserMatch
     */
    protected Model $model;

    /**
     * MatchRepository constructor.
     *
     * @param UserMatch $model The match model used for database operations.
     */
    public function __construct(UserMatch $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Retrieve all active matches for a given user as UserProfile models.
     *
     * First fetches the raw match records where the user is either party and status is ACTIVE.
     * Then extracts the "other" user's ID from each match and queries the UserProfile table for those IDs.
     *
     * @param int $userId The ID of the authenticated user.
     * @return Collection Collection of {@see \App\Models\UserProfile} models for matched users.
     */
    public function getMatchesForUser(int $userId): Collection
    {
        $matches = $this->getMatchesCollection($userId);

        if ($matches->count() == 0) {
            return collect([]);
        }

        /** @var array<int, int> $otherUserIdToMatchId maps matched user's id → user_matches.id */
        $otherUserIdToMatchId = [];
        foreach ($matches as $match) {
            $otherUserId = $match->user_id == $userId
                ? $match->matched_user_id
                : $match->user_id;
            $otherUserIdToMatchId[(int) $otherUserId] = (int) $match->id;
        }

        $profiles = UserProfile::whereIn('user_id', array_keys($otherUserIdToMatchId))->get();

        return $profiles->map(function (UserProfile $profile) use ($otherUserIdToMatchId): ?UserProfile {
            $matchId = $otherUserIdToMatchId[(int) $profile->user_id] ?? null;
            if ($matchId === null) {
                return null;
            }
            $profile->setAttribute('user_match_id', $matchId);

            return $profile;
        })->filter()->values();
    }

    /**
     * Fetch raw match records where the user is involved and the match is active.
     *
     * @param int $userId The ID of the authenticated user.
     * @return Collection Collection of {@see \App\Models\UserMatch} models.
     */
    private function getMatchesCollection(int $userId): Collection
    {
        return $this->model
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('matched_user_id', $userId);
            })
            ->where('status', $this->model::USER_MATCH_STATUS_ACTIVE)
            ->get();
    }

    /**
     * Unmatch (soft‑delete) a specific match record.
     *
     * The method performs the following in a transaction:
     * - Finds the match record by ID.
     * - Updates its status to `UNMATCHED`.
     * - Records which user initiated the unmatch (`unmatched_by_user_id`).
     * - Calls the parent `delete()` method to soft‑delete the match (if `SoftDeletes` is used).
     *
     * @param int $unmatchedByUserId The ID of the user performing the unmatch.
     * @param int $userMatchId The primary key of the match to unmatch.
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the match does not exist.
     */
    public function unmatch(int $unmatchedByUserId, int $userMatchId): void
    {
        $userMatch = $this->model->findOrFail($userMatchId);

        DB::transaction(function () use ($userMatch, $unmatchedByUserId) {
            $userMatch->status = $this->model::USER_MATCH_STATUS_UNMATCHED;
            $userMatch->unmatched_by_user_id = $unmatchedByUserId;
            $userMatch->save();

            $this->delete($userMatch->id);
        });
    }

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
    public function getPotentialMatches(int $userId, float $lat, float $lng, int $maxDistanceKm = 50, int $limit = 50): array
    {
        $results = DB::select(
            'SELECT * FROM get_potential_matches_full(?, ?, ?, ?, ?)',
            [$userId, $lat, $lng, $maxDistanceKm, $limit]
        );

        return $results;
    }
}
