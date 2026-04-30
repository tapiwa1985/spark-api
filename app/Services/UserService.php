<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;

class UserService extends BaseService implements UserServiceInterface
{
    /**
     * @var UserRepositoryInterface $userRepository
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * UserService constructor.
     *
     * @param Repositories\Contracts\UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);

        $this->userRepository = $userRepository;
    }

    /**
     * Get a user by their email address.
     *
     * @param string $email
     * @return Model|null
     */
    public function getUserByEmail(string $email): ?Model
    {
        return $this->repository->findByEmail($email);
    }

     /**
     * Get a user by their LinkedIn ID.
     *
     * @param string $linkedinId
     * @return Model|null
     */
    public function fetchByLinkedInId(string $linkedinId): ?Model
    {
        return $this->repository->findByLinkedInId($linkedinId);
    }
}
