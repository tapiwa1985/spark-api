<?php

namespace App\Services;

use App\Services\Contracts\UserRejectionServiceInterface;
use App\Models\UserRejection;
use App\Repositories\Contracts\UserRejectionRepositoryInterface;

/**
 * Service responsible for managing user rejection logic.
 *
 * Handles business operations related to user rejections (e.g., rejecting
 * matches, content, or other users). Implements the contract defined
 * by UserRejectionServiceInterface.
 *
 * @package App\Services
 */
class UserRejectionService extends BaseService implements UserRejectionServiceInterface
{
    /**
     * The repository instance for user rejection data operations.
     *
     * @var UserRejectionRepositoryInterface
     */
    protected UserRejectionRepositoryInterface $userRejectionRepository;

    /**
     * Create a new UserRejectionService instance.
     *
     * @param UserRejectionRepositoryInterface $userRejectionRepository
     */
    public function __construct(UserRejectionRepositoryInterface $userRejectionRepository)
    {
        parent::__construct($userRejectionRepository);
    }
}
