<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LikeUserRequest;
use App\Http\Resources\UserProfileResourceCollection;
use App\Services\Contracts\LikeServiceInterface;

/**
 * Controller for handling like-related operations in the dating application.
 *
 * Manages user interactions where one user expresses interest in another
 * user (liking) and retrieves lists of users who have received likes.
 *
 * @package App\Http\Controllers\Api
 */
class LikeController extends Controller
{
    /**
     * The like service instance.
     *
     * @var LikeServiceInterface
     */
    private LikeServiceInterface $_likeService;

    /**
     * LikeController constructor.
     *
     * @param LikeServiceInterface $likeService The service handling like business logic
     *
     * @return void
     */
    public function __construct(LikeServiceInterface $likeService)
    {
        $this->_likeService = $likeService;
    }

    /**
     * Display a list of users who have liked the authenticated user.
     *
     * Retrieves all users who have sent a "like" to the currently authenticated user.
     * Returns a collection of user profiles with all relevant relationships loaded
     * (interests, languages, profile images, etc.) for display in the matches/likes screen.
     *
     * @return UserProfileResourceCollection A collection of UserProfile resources
     */
    public function index(): UserProfileResourceCollection
    {
        $userProfile = auth()->user()->userProfile;
        $userProfiles = $this->_likeService->getReceivedLikes($userProfile->user->id);

        return new UserProfileResourceCollection($userProfiles);
    }

    /**
     * Create a new like (express interest in another user).
     *
     * Records that the authenticated user has liked another user.
     * This is typically triggered when a user swipes right or taps the like button
     * on another user's profile.
     *
     * If the liked user has already liked the authenticated user, this will create
     * a mutual match (the service layer should handle match creation separately).
     *
     * @param LikeUserRequest $request The validated request containing the liked_user_id
     *
     * @return JsonResponse A JSON response with an empty object and HTTP 201 Created status
     */
    public function store(LikeUserRequest $request): JsonResponse
    {
        $data = $request->only('liked_user_id');
        $userProfile = auth()->user()->userProfile;
        $data['user_id'] = $userProfile->user->id;

        $like = $this->_likeService->create($data);

        return response()->json([], JsonResponse::HTTP_OK);
    }
}
