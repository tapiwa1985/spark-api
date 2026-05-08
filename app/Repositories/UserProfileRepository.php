<?php

namespace App\Repositories;

use App\Models\UserProfile;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * {@see UserProfile} persistence with email lookup across the owning {@see \App\Models\User}.
 */
class UserProfileRepository extends BaseRepository implements UserProfileRepositoryInterface
{
    /**
     * @param UserProfile $model Profile aggregate root.
     */
    public function __construct(UserProfile $model)
    {
        parent::__construct($model);
    }

    /**
     * Loads profile + `user` + `interests` where the related user email matches.
     */
    public function findByEmail(string $email): ?UserProfile
    {
        return $this->model
            ->whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })
            ->with(['user', 'interests'])
            ->first();
    }

    public function getCoordinatesForUser(int $userId): ?array
    {
        $row = DB::table('user_profiles')
            ->selectRaw('ST_Y(location::geometry) as latitude, ST_X(location::geometry) as longitude')
            ->where('user_id', $userId)
            ->whereNotNull('location')
            ->first();

        if ($row === null) {
            return null;
        }

        return [
            'latitude' => (float) $row->latitude,
            'longitude' => (float) $row->longitude,
        ];
    }
}
