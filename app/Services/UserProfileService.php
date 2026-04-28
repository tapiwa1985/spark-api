<?php

namespace App\Services;

use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

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
                'password' => $data['password'],
            ]);

            return parent::create([
                'user_id' => $user->id,
                'bio' => $data['bio'],
                'dob' => $data['dob'],
                'gender' => $data['gender'],
            ]);
        });
    }
}
