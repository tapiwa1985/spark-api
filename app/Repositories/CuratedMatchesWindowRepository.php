<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\CuratedMatchesWindow;
use App\Repositories\Contracts\CuratedMatchesWindowRepositoryInterface;
use Carbon\CarbonImmutable;

/**
 * Class CuratedMatchesWindowRepository
 *
 * Repository class for handling database operations related to curated matches windows.
 * Extends the base repository and implements the curated matches window repository interface.
 *
 * @package App\Repositories
 */
class CuratedMatchesWindowRepository extends BaseRepository implements CuratedMatchesWindowRepositoryInterface
{
    /**
     * @var Model The model instance for curated matches window
     */
    protected Model $model;

    /**
     * CuratedMatchesWindowRepository constructor.
     *
     * @param CuratedMatchesWindow $model The curated matches window model instance
     */
    public function __construct(CuratedMatchesWindow $model)
    {
        parent::__construct($model);
    }

    public function expireActiveWindowsBefore(CarbonImmutable $periodStart): int
    {
        return $this->model
            ->newQuery()
            ->where('status', 'ACTIVE')
            ->where('starts_at', '<', $periodStart)
            ->update(['status' => 'EXPIRED']);
    }

    public function findActiveForUser(int $userId): ?CuratedMatchesWindow
    {
        return $this->model
            ->newQuery()
            ->where('user_id', $userId)
            ->where('status', 'ACTIVE')
            ->latest('starts_at')
            ->first();
    }

    public function firstOrCreateForUserPeriod(
        int $userId,
        CarbonImmutable $windowStart,
        CarbonImmutable $windowEnd,
        int $maxItems
    ): CuratedMatchesWindow {
        /** @var CuratedMatchesWindow $window */
        $window = $this->model->newQuery()->firstOrCreate(
            [
                'user_id' => $userId,
                'starts_at' => $windowStart,
                'ends_at' => $windowEnd,
            ],
            [
                'max_items' => $maxItems,
                'status' => 'ACTIVE',
            ]
        );

        if ($window->status !== 'ACTIVE' || (int) $window->max_items !== $maxItems) {
            $window->fill([
                'status' => 'ACTIVE',
                'max_items' => $maxItems,
            ])->save();
        }

        return $window;
    }
}
