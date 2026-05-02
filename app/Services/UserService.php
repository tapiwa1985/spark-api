<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;

/**
 * User-centric lookups beyond generic CRUD: email and LinkedIn identity resolution for auth flows.
 */
class UserService extends BaseService implements UserServiceInterface
{
    /**
     * Typed alias of {@see BaseService::$repository} as {@see UserRepositoryInterface}.
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository User aggregate with custom finders.
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);

        $this->userRepository = $userRepository;
    }

    /**
     * @return Model|null {@see UserRepositoryInterface::findByEmail}
     */
    public function getUserByEmail(string $email): ?Model
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * @return Model|null {@see UserRepositoryInterface::findByLinkedInId}
     */
    public function fetchByLinkedInId(string $linkedinId): ?Model
    {
        return $this->repository->findByLinkedInId($linkedinId);
    }
}
