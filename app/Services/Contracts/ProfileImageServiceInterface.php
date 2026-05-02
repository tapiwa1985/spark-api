<?php

namespace App\Services\Contracts;

interface ProfileImageServiceInterface extends BaseServiceInterface
{
    /**
     * @param int $userProfileId
     * @param int $profileImageId
     * @return void
     */
    public function setDisplayImage(int $userProfileId, int $profileImageId): void;
}
