<?php

namespace App\Services;

use App\Repositories\Contracts\BlockedUserRepositoryInterface;
use App\Services\Contracts\BlockedUserServiceInterface;

/**
 * Class BlockedUserService
 *
 * Implements the business logic for managing blocked user relationships.
 * This service acts as an intermediary between controllers and the blocked user repository,
 * enforcing application rules such as preventing self-blocks, validating input,
 * and coordinating with other services (e.g., match service, chat service) when a block occurs.
 *
 * Extends the base service to inherit common service methods and relies on the
 * blocked user repository for data persistence.
 *
 * @package App\Services
 */
class BlockedUserService extends BaseService implements BlockedUserServiceInterface
{
    /**
     * The repository instance responsible for blocked user data operations.
     *
     * @var BlockedUserRepositoryInterface
     */
    protected BlockedUserRepositoryInterface $blockedUserRepository;

    /**
     * BlockedUserService constructor.
     *
     * Injects the blocked user repository and passes it to the parent BaseService.
     * The parent constructor typically sets up the repository for generic CRUD operations.
     *
     * @param BlockedUserRepositoryInterface $blockedUserRepository Repository for blocked user persistence.
     */
    public function __construct(BlockedUserRepositoryInterface $blockedUserRepository)
    {
        parent::__construct($blockedUserRepository);
    }
}
