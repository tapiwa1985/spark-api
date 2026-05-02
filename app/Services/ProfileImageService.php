<?php

namespace App\Services;

use App\Repositories\Contracts\ProfileImageRepositoryInterface;
use App\Services\Contracts\ProfileImageServiceInterface;

class ProfileImageService extends BaseService implements ProfileImageServiceInterface
{
    /**
     * @var ProfileImageRepositoryInterface
     */
    protected ProfileImageRepositoryInterface $profileImageRepository;

    /**
     * @param ProfileImageRepositoryInterface
     */
    public function __construct(ProfileImageRepositoryInterface $profileImageRepository)
    {
        parent::__construct($profileImageRepository);

        $this->profileImageRepository = $profileImageRepository;
    }

    /**
     * @param int $userProfileId
     * @param int $profileImageId
     * @return void
     */
    public function setDisplayImage(int $userProfileId, int $profileImageId): void
    {
        $this->profileImageRepository->setDisplayImage($userProfileId, $profileImageId);
    }
}
