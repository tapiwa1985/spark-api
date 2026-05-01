<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Contracts\UserProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserProfileResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Support\Facades\Gate;

/**
 * Controller responsible for handling user profile creation and management.
 * This controller relies on a user profile service to handle the business logic of creating and managing user profiles.
 * The store method validates the incoming registration request, creates a new user profile using the service, generates
 * a JWT token for the newly created user, and returns a JSON response containing the user profile data and authentication token.
 *
 * @package App\Http\Controllers\Api
 */
class UserProfileController extends Controller
{
    /**
     * @var UserProfileServiceInterface $userProfileService
     */
    private UserProfileServiceInterface $_userProfileService;

    /**
     * UserProfileController constructor.
     *
     * @param UserProfileServiceInterface $userProfileService
     */
    public function __construct(UserProfileServiceInterface $userProfileService)
    {
        $this->_userProfileService = $userProfileService;
    }

    /**
     * @param string $userProfileId
     * @param UpdateUserProfileRequest $request
     *
     * @return UserProfileResource
     */
    public function update(UpdateUserProfileRequest $request): UserProfileResource
    {
        $data = $request->only('job_title', 'industry_id', 'gender');

        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $userProfile = $this->_userProfileService->update((int)$userProfile->id, $data);

        return new UserProfileResource($userProfile);
    }
}
