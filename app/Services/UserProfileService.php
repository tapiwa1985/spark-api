<?php

namespace App\Services;

use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * Application services around {@see UserProfile}: transactional creation with {@see User}, lookup by email, interest sync, and generic updates via the repository.
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
     * @param UserRepositoryInterface         $userRepo        Used when creating a user + profile together.
     * @param UserProfileRepositoryInterface    $userProfileRepo Passed to {@see BaseService} as the primary repository for CRUD on profiles.
     */
    public function __construct(UserRepositoryInterface $userRepo, UserProfileRepositoryInterface $userProfileRepo)
    {
        parent::__construct($userProfileRepo);

        $this->userProfileRepo = $userProfileRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Create a {@see User} and their {@see UserProfile} in one DB transaction (registration-style payload).
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
                'dob' => $data['dob'] ?? null,
            ]);
        });
    }

    /**
     * Resolve the profile whose owning user has the given email (includes relations as defined on the repository query).
     */
    public function fetchByEmail(string $email): ?UserProfile
    {
        return $this->userProfileRepo->findByEmail($email);
    }

    /**
     * Add interest IDs to the pivot; existing links remain (see {@see \Illuminate\Database\Eloquent\Relations\BelongsToMany::syncWithoutDetaching}).
     */
    public function addInterests(int $userProfileId, array $interestIds): UserProfile
    {
        $userProfile = $this->userProfileRepo->find($userProfileId);

        $userProfile->interests()->syncWithoutDetaching($interestIds);

        return $userProfile->fresh('interests');
    }

    /**
     * Add language IDs to the profile pivot without removing existing languages.
     */
    public function addLanguages(UserProfile $userProfile, array $languageIds): UserProfile
    {
        $userProfile->languages()->syncWithoutDetaching($languageIds);

        return $userProfile->fresh('languages');
    }
}
