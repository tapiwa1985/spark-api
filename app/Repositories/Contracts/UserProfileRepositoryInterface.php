<?php

namespace App\Repositories\Contracts;

use App\Models\UserProfile;
use App\Repositories\Contracts\BaseRepositoryInterface;

/**
 * Interface UserProfileRepositoryInterface
 *
 * @package App\Repositories\Contracts
 */
interface UserProfileRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param string $email
     * @return UserProfile|null
     */
    public function findByEmail(string $email): ?UserProfile;
}
