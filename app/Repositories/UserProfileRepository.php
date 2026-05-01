<?php

namespace App\Repositories;

use App\Models\UserProfile;
use App\Repositories\Contracts\UserProfileRepositoryInterface;

/**
 * Class UserProfileRepository
 * @package App\Repositories
 */
class UserProfileRepository extends BaseRepository implements UserProfileRepositoryInterface
{
    /**
     * UserProfileRepository constructor.
     *
     * @param UserProfile $model
     */
    public function __construct(UserProfile $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $email
     * @return Model|null
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
}
