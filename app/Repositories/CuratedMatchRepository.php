<?php

namespace App\Repositories;

use App\Models\CuratedMatch;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\CuratedMatchRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Class CuratedMatchRepository
 *
 * Repository class for handling database operations related to curated matches.
 * Extends the base repository and implements the curated match repository interface.
 *
 * @package App\Repositories
 */
class CuratedMatchRepository extends BaseRepository implements CuratedMatchRepositoryInterface
{
    /**
     * @var Model The model instance for curated match
     */
    protected Model $model;

    /**
     * CuratedMatchRepository constructor.
     *
     * @param CuratedMatch $model The curated match model instance
     */
    public function __construct(CuratedMatch $model)
    {
        parent::__construct($model);
    }

    public function deleteForWindow(int $windowId): int
    {
        return $this->model
            ->newQuery()
            ->where('curated_matches_window_id', $windowId)
            ->delete();
    }

    public function insertRows(array $rows): void
    {
        if (count($rows) === 0) {
            return;
        }

        $this->model->newQuery()->insert($rows);
    }

    public function fetchForActiveWindowUserId(int $activeWindowId): Collection
    {
        return $this->model
            ->newQuery()
            ->where('curated_matches_window_id', $activeWindowId)
            ->with([
                'user.userProfile.user',
                'user.userProfile.industry',
                'user.userProfile.interests',
                'user.userProfile.languages',
                'user.userProfile.profileImages',
            ])
            ->orderByDesc('rank_score')
            ->get();
    }
}
