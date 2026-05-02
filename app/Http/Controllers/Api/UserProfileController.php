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
use App\Http\Resources\ProfileImageResource;
use App\Services\Contracts\ProfileImageServiceInterface;
use App\Http\Requests\UploadProfileImageRequest;
use Illuminate\Support\Facades\Storage;
use Clickbar\Magellan\Data\Geometries\Point;
use App\Http\Requests\UpdateUserLocationRequest;

/**
 * Authenticated API actions for the signed-in user’s {@see \App\Models\UserProfile}: core fields, interests, bio,
 * gallery images (upload, display image, delete), and geographic location. Authorization uses the profile policy on the
 * current user’s profile for every mutating action.
 *
 * @package App\Http\Controllers\Api
 */
class UserProfileController extends Controller
{
    /**
     * Persists profile field updates, interest tags, and location through the domain service.
     */
    private UserProfileServiceInterface $_userProfileService;

    /**
     * Encodes and uploads binary image data to object storage, returning a public URL for persistence.
     */
    private ImageUploaderInterface $_imageUploadService;

    /**
     * Creates and manages {@see \App\Models\ProfileImage} rows (display order, primary flag, deletion orchestration).
     */
    private ProfileImageServiceInterface $_profileImageService;

    /**
     * @param UserProfileServiceInterface    $userProfileService   Updates profile attributes and interest pivots.
     * @param ImageUploaderInterface         $imageUploaderService Stores resized uploads on the `gcs` disk.
     * @param ProfileImageServiceInterface   $profileImageService  CRUD and display-image rules for gallery rows.
     */
    public function __construct(
        UserProfileServiceInterface $userProfileService,
        ImageUploaderInterface $imageUploaderService,
        ProfileImageServiceInterface $profileImageService
    ) {
        $this->_userProfileService = $userProfileService;
        $this->_imageUploadService = $imageUploaderService;
        $this->_profileImageService = $profileImageService;
    }

    /**
     * Updates job title, industry, and gender for the authenticated user’s profile (validated by {@see UpdateUserProfileRequest}).
     *
     * @return UserProfileResource Fresh profile payload after {@see UserProfileServiceInterface::update}.
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
     * Attaches one or more interest IDs to the profile without removing existing interests (pivot add-only semantics).
     *
     * @return UserProfileResource Profile with the `interests` relation refreshed.
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
     * Sets the profile biography text (max length enforced in inline validation).
     *
     * @return UserProfileResource Updated profile including the new `bio` value.
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

    /**
     * Accepts a multipart image, uploads it via {@see ImageUploaderInterface}, creates a {@see \App\Models\ProfileImage}
     * row with URL and optional display metadata, and returns that image as JSON.
     *
     * @return ProfileImageResource The newly created gallery image resource.
     */
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

    /**
     * Marks the given gallery image as the primary display photo for this profile (delegates to the profile-image service).
     *
     * @param string $imageId Route segment identifying the {@see \App\Models\ProfileImage} primary key.
     * @return void
     */
    public function setDisplayImage(string $imageId)
    {
        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $this->_profileImageService->setDisplayImage($userProfile->id, (int)$imageId);
    }

    /**
     * Deletes a gallery image row when it belongs to the flow’s authorization context; if deletion succeeds, attempts to
     * remove the object from the `gcs` disk using the stored URL/key.
     *
     * @param string $imageId Gallery image primary key.
     * @return JsonResponse       `204` when removed, `404` when missing or not deleted.
     */
    public function deleteImage(string $imageId): JsonResponse
    {
        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $image = $this->_profileImageService->find((int)$imageId);
        if ($image) {
            $deleted = $this->_profileImageService->delete((int)$imageId);

            if ($deleted) {
                Storage::disk('gcs')->delete($image->image_url);

                return response()->json([], Response::HTTP_NO_CONTENT);
            }
        }

        return response()->json([], Response::HTTP_NOT_FOUND);
    }

    /**
     * Persists latitude and longitude as a PostGIS {@see Point} on the profile via {@see UserProfileServiceInterface::update},
     * then returns the profile with `user` and `industry` relations loaded for the resource transformer.
     *
     * @return UserProfileResource GeoJSON-capable profile payload after location write.
     */
    public function updateLocation(UpdateUserLocationRequest $request): UserProfileResource
    {
        $userProfile = auth()->user()->userProfile;
        Gate::authorize('update', $userProfile);

        $updatedProfile = $this->_userProfileService->update((int) $userProfile->id, [
            'location' => Point::makeGeodetic(
                (float) $request->input('latitude'),
                (float) $request->input('longitude')
            ),
        ]);

        return new UserProfileResource($updatedProfile->fresh(['user', 'industry']));
    }
}
