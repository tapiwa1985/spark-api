<?php

namespace App\Repositories\Contracts;

interface ProfileImageRepositoryInterface extends BaseRepositoryInterface
{
    public function setDisplayImage(int $usperProfileId, int $profileImageId): void;
}
