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
use App\Utils\Contracts\ImageUploaderInterface;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ProfileImageResource;
use App\Services\Contracts\ProfileImageServiceInterface;
use App\Http\Requests\UploadProfileImageRequest;

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
     * @var ImageUploaderInterface $_imageUploadService
     */
    private ImageUploaderInterface $_imageUploadService;

    private ProfileImageServiceInterface $_profileImageService;

    /**
     * UserProfileController constructor.
     *
     * @param UserProfileServiceInterface $userProfileService
     * @param ImageUploaderInterface $imageUploaderService
     */
    public function __construct(UserProfileServiceInterface $userProfileService, ImageUploaderInterface $imageUploaderService, ProfileImageServiceInterface $profileImageService)
    {
        $this->_userProfileService = $userProfileService;
        $this->_imageUploadService = $imageUploaderService;
        $this->_profileImageService = $profileImageService;
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

    /**
     * @param Request $request
     *
     * @return UserProfileResource
     */
    public function addInterests(Request $request): UserProfileResource
    {
        $data = $request->validate([
            'interest_ids' => 'required|array',
            'interest_ids.*' => 'exists:interests,id',
        ]);

        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $userProfile = $this->_userProfileService->addInterests((int)$userProfile->id, $data['interest_ids']);

        return new UserProfileResource($userProfile);
    }

    /**
     * @param Request $request
     *
     * @return UserProfileResource
     */
    public function updateBio(Request $request): UserProfileResource
    {
        $data = $request->validate([
            'bio' => 'required|string|max:500',
        ]);

        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $userProfile = $this->_userProfileService->update((int)$userProfile->id, ['bio' => $data['bio']]);

        return new UserProfileResource($userProfile);
    }

    public function uploadProfilePicture(UploadProfileImageRequest $request): ProfileImageResource
    {
        $data = $request->only('image', 'display_order', 'is_display');

        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $data['user_profile_id'] = $userProfile->id;

        $imageUrl = $this->_imageUploadService->uploadImage($data['image']);
        $data['image_url'] = $imageUrl;
        $profileImage = $this->_profileImageService->create($data);

        return new ProfileImageResource($profileImage);
    }
}
