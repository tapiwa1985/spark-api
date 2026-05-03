<?php

namespace App\Repositories;

use App\Models\Like;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\LikeRepositoryInterface;

/**
 * Repository for managing user likes and interactions.
 *
 * Handles database operations related to likes, including retrieving
 * users who have liked a specific user. This repository acts as an
 * intermediary between the database and the service layer for all
 * like-related operations.
 *
 * @package App\Repositories
 */
class LikeRepository extends BaseRepository implements LikeRepositoryInterface
{
    /**
     * @var Model The underlying Like model instance
     */
    protected Model $model;

    /**
     * LikeRepository constructor.
     *
     * Initializes the repository with the Like model and calls the
     * parent constructor to set up base repository functionality.
     *
     * @param Like $model The Like model instance to use for database operations
     *
     * @return void
     */
    public function __construct(Like $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    /**
     * Fetch a collection of user profiles who have liked the specified user.
     *
     * Retrieves all users who have sent a "like" to the given user ID.
     * The method first queries the likes table to get all user IDs that
     * have liked the target user, then fetches their complete user profiles
     * with eager-loaded relationships.
     *
     * @param int $userId The ID of the user who received the likes
     *
     * @return Collection<int, UserProfile> A collection of UserProfile models.
     */
    public function getReceivedLikes(int $userId): Collection
    {
        $likingUserIds = $this->model
            ->where('liked_user_id', $userId)
            ->pluck('user_id');

        return UserProfile::whereIn('user_id', $likingUserIds)
            ->with([
                'user',
                'interests',
                'languages',
                'profileImages',
                'industry'
            ])
            ->get();
    }
}
