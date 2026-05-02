<?php

namespace App\Services;

use App\Repositories\Contracts\ProfileImageRepositoryInterface;
use App\Services\Contracts\ProfileImageServiceInterface;

/**
 * Gallery CRUD plus {@see ProfileImageRepositoryInterface::setDisplayImage} orchestration for profile photos.
 */
class ProfileImageService extends BaseService implements ProfileImageServiceInterface
{
    /**
     * Same concrete repository as {@see BaseService::$repository}, exposed for display-order logic.
     */
    protected ProfileImageRepositoryInterface $profileImageRepository;

    /**
     * @param ProfileImageRepositoryInterface $profileImageRepository Profile image persistence.
     */
    public function __construct(ProfileImageRepositoryInterface $profileImageRepository)
    {
        parent::__construct($profileImageRepository);

        $this->profileImageRepository = $profileImageRepository;
    }

    /**
     * Reorders gallery rows so the chosen image is first and flagged `is_display`.
     */
    public function setDisplayImage(int $userProfileId, int $profileImageId): void
    {
        $this->profileImageRepository->setDisplayImage($userProfileId, $profileImageId);
    }
}
