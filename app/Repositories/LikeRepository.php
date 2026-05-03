<?php

namespace App\Repositories;

use App\Models\Like;
use Illuminate\Support\Collection;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\LikeRepositoryInterface;

class LikeRepository extends BaseRepository implements LikeRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param Like $model
     */
    public function __construct(Like $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    /**
     * Fetches a collection of received likes
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getReceivedLikes(int $userId): Collection
    {
        $likingUserIds = $this->model
           ->where('liked_user_id', $userId)
           ->pluck('user_id');

        return UserProfile::whereIn('user_id', $likingUserIds)
           ->with(['user', 'interests', 'languages', 'profileImages', 'industry'])
           ->get();
    }
}
