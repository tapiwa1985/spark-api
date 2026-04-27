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
}
