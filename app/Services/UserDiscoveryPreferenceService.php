<?php

namespace App\Services;

use App\Services\Contracts\UserDiscoveryPreferenceServiceInterface;
use App\Repositories\Contracts\UserDiscoveryPreferenceRepositoryInterface;

/**
 * Class UserDiscoveryPreferenceService
 *
 * Service class for managing user discovery preferences.
 * Implements the UserDiscoveryPreferenceServiceInterface and extends the BaseService.
 *
 * @package App\Services
 */
class UserDiscoveryPreferenceService extends BaseService implements UserDiscoveryPreferenceServiceInterface
{
    /**
     * @var UserDiscoveryPreferenceRepositoryInterface
     */
    protected UserDiscoveryPreferenceRepositoryInterface $userDiscoveryPreferenceRepository;

    /**
     * UserDiscoveryPreferenceService constructor.
     *
     * @param UserDiscoveryPreferenceRepositoryInterface $userDiscoveryPreferenceRepository
     */
    public function __construct(UserDiscoveryPreferenceRepositoryInterface $userDiscoveryPreferenceRepository)
    {
        parent::__construct($userDiscoveryPreferenceRepository);
    }
}
