<?php

namespace App\Repositories;

use App\Models\UserMatch;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\MatchRepositoryInterface;

class MatchRepository extends BaseRepository implements MatchRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * MatchRepository class constructor
     *
     * @param UserMatch
     */
    public function __construct(UserMatch $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

     /**
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getMatchesForUser(int $userId): Collection
    {
        $matches = $this->getMatchesCollection($userId);

        if ($matches->count() == 0) {
            return collect([]);
        }

        $matchIds = $matches->map(function ($match) use ($userId) {
                return $match->user_id == $userId
                    ? $match->matched_user_id
                    : $match->user_id;
        });

        return UserProfile::whereIn('user_id', $matchIds)->get();
    }

    /**
     * @param int $userId
     * @retun Collection
     */
    private function getMatchesCollection(int $userId): Collection
    {
        return $this->model
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('matched_user_id', $userId);
            })
            ->where('status', UserMatch::USER_MATCH_STATUS_ACTIVE)
            ->get();
    }
}
