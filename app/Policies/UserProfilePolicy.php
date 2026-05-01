<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;

class UserProfilePolicy
{
    /**
     * Determine if the given user can update the specified user profile.
      *
      * @param User $user
      * @param UserProfile $userProfile
      *
      * @return bool
     */
    public function update(User $user, UserProfile $userProfile): bool
    {
        if (!$user->userProfile) {
            return false;
        }

        return $user->userProfile->id === $userProfile->id;
    }
}
