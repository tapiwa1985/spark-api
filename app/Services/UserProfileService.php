<?php

namespace App\Services;

use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class UserProfileService
 * @package App\Services
 */
class UserProfileService extends BaseService implements UserProfileServiceInterface
{
    /**
     * @var UserRepositoryInterface $userRepo
     */
    protected UserRepositoryInterface $userRepo;

    /**
     * @var UserProfileRepositoryInterface $userProfileRepo
     */
    protected UserProfileRepositoryInterface $userProfileRepo;

    /**
     * UserProfileService constructor
     *
     * @param UserProfileRepository $userRepo
     * @param UserProfileRepositoryInterface $userProfileRepo
     */
    public function __construct(UserRepositoryInterface $userRepo, UserProfileRepositoryInterface $userProfileRepo)
    {
        parent::__construct($userProfileRepo);

        $this->userProfileRepo = $userProfileRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Creates a user profle. A profile belongs to a user.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepo->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'] ?? null,
                'linkedin_id' => $data['linkedin_id'] ?? null,
                'linkedin_token' => $data['linkedin_token'] ?? null,
                'linkedin_refresh_token' => $data['linkedin_refresh_token'] ?? null,
            ]);

            return parent::create([
                'user_id' => $user->id,
                'dob' => $data['dob'],
            ]);
        });
    }

    /**
     * Get a profile by user email.
     *
     * @param string $email
     * @return UserProfile|null
     */
    public function fetchByEmail(string $email): ?UserProfile
    {
        return $this->userProfileRepo->findByEmail($email);
    }
}
