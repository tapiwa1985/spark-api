<?php

namespace App\Repositories\Contracts;

interface ProfileImageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $userProfileId
     * @param int $profileImageId
     * @return void
     */
    public function setDisplayImage(int $usperProfileId, int $profileImageId): void;
}
