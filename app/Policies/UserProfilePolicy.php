<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;

/**
 * Ensures profile mutations apply only to the authenticated member’s own {@see UserProfile} row.
 */
class UserProfilePolicy
{
    /**
     * @return bool True when `$user` owns `$userProfile` (matching nested profile id).
     */
    public function update(User $user, UserProfile $userProfile): bool
    {
        if (!$user->userProfile) {
            return false;
        }

        return $user->userProfile->id === $userProfile->id;
    }
}
