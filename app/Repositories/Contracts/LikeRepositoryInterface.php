<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface LikeRepositoryInterface
 *
 * Repository interface for managing user likes and matches.
 * Handles operations related to liking other users, receiving likes,
 * and retrieving like-based interactions.
 *
 * @package App\Repositories\Contracts
 */
interface LikeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all users who have liked the specified user.
     *
     * Retrieves a collection of user profiles who have sent a "like" to the given user.
     * The returned collection typically includes eager-loaded relationships such as
     * interests and languages to provide complete profile information for display.
     *
     * @param int $userId The ID of the user who received the likes
     *
     * @return Collection A collection of UserProfile models representing users who liked this user
     */
    public function getReceivedLikes(int $userId): Collection;
}
