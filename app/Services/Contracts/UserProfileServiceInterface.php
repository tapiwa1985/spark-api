<?php

namespace App\Services\Contracts;

use App\Models\UserProfile;

interface UserProfileServiceInterface extends BaseServiceInterface
{
    /**
     * Get a user profile by the owning user's email.
     *
     * @param string $email
     * @return UserProfile|null
     */
    public function fetchByEmail(string $email): ?UserProfile;
}
