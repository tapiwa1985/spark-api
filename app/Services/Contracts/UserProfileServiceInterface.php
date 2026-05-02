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

    /**
     * Add interest IDs to the profile pivot without removing existing interests.
     */
    public function addInterests(int $userProfileId, array $interestIds): UserProfile;

    /**
     * @param UserProfile $userProfile
     * @param array $languageIds
     * @return UserProfile
     */
    public function addLanguages(UserProfile $userProfile, array $languageIds): UserProfile;
}
