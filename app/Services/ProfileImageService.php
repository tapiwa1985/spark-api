<?php 

namespace App\Services;

use App\Repositories\Contracts\ProfileImageRepositoryInterface;
use App\Services\Contracts\ProfileImageServiceInterface;

class ProfileImageService extends BaseService implements ProfileImageServiceInterface 
{
    protected ProfileImageRepositoryInterface $profileImageRepository;

    public function __construct(ProfileImageRepositoryInterface $profileImageRepository)
    {
        parent::__construct($profileImageRepository);

        $this->profileImageRepository = $profileImageRepository;
    }
}