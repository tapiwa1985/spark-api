<?php

namespace App\Services;

use App\Services\Contracts\LikeServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Events\MatchCreated;
use App\Repositories\Contracts\LikeRepositoryInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Service class for managing user like operations.
 *
 * Handles the business logic for user interactions including:
 * - Creating new likes between users
 * - Checking for mutual likes and creating matches
 * - Retrieving lists of users who have received likes
 *
 * This service acts as an intermediary between controllers and repositories,
 * containing the core matching logic that determines when two users create a match.
 *
 * @package App\Services
 */
class LikeService implements LikeServiceInterface
{
    /**
     * The like repository instance for database operations.
     *
     * @var LikeRepositoryInterface
     */
    protected LikeRepositoryInterface $repo;

    /**
     * The match repository instance for creating match records.
     *
     * @var MatchRepositoryInterface
     */
    private MatchRepositoryInterface $matchRepository;

    /**
     * LikeService constructor.
     *
     * Initializes the service with required repository dependencies.
     *
     * @param LikeRepositoryInterface $repository Repository for like-related database operations
     * @param MatchRepositoryInterface $matchRepository Repository for match-related database operations
     *
     * @return void
     */
    public function __construct(LikeRepositoryInterface $repository, MatchRepositoryInterface $matchRepository)
    {
        $this->repo = $repository;
        $this->matchRepository = $matchRepository;
    }

    /**
     * Get all users who have liked a specific user.
     *
     * Retrieves a collection of user profiles representing users who have
     * expressed interest in the given user. This is typically used to display
     * the "likes received" screen in the application.
     *
     * @param int $userId The ID of the user who received the likes
     *
     * @return Collection<int, \App\Models\UserProfile> A collection of user profiles
     *         who have liked the specified user. Returns an empty collection if
     *         no likes are found.
     */
    public function getReceivedLikes(int $userId): Collection
    {
        return $this->repo->getReceivedLikes($userId);
    }

    /**
     * Create a new like or create a mutual match if one already exists.
     *
     * This method implements the core matching logic:
     * 1. When User A likes User B, it checks if User B has already liked User A
     * 2. If yes (mutual like), it creates a match record and updates both likes
     * 3. If no, it simply creates a new like record
     *
     * The mutual detection prevents duplicate matches and ensures that matches
     * are only created when both users express mutual interest.
     *
     * @param array $data The like data containing user_id and liked_user_id
     *                    Format: ['user_id' => int, 'liked_user_id' => int]
     *
     * @return Model The created or updated like model. If a mutual like was detected,
     *               returns the existing mutual like record. Otherwise returns the
     *               newly created like record.
     */
    public function create(array $data): Model
    {
        $mutualLike = $this->repo->findMutualLike($data['user_id'], $data['liked_user_id']);

        if ($mutualLike) {
            return DB::transaction(function () use ($data, $mutualLike) {
                $userMatch = $this->matchRepository->create([
                    'user_id' => $data['user_id'],
                    'matched_user_id' => $data['liked_user_id'],
                ]);

                $this->repo->update($mutualLike->id, ['matched_at' => now()]);
                $mutualLike->refresh();

                broadcast(new MatchCreated($userMatch));

                return $mutualLike;
            });
        }

        return $this->repo->create($data);
    }
}
