<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Http\Resources\UserProfileResource;
use App\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible for handling user authentication, including login and token generation.
 * This controller uses JWT for authentication and relies on a user service to retrieve user information.
 * The login method validates the incoming request, attempts to authenticate the user, and returns a JSON response containing the JWT token and user information if successful. If authentication fails, it returns an unauthorized error response.
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * The user profile service instance.
     * @var UserProfileServiceInterface
     */
    private UserProfileServiceInterface $_userProfileService;

    /**
     * Create a new controller instance.
     *
     * @param UserProfileServiceInterface $userProfileService
     */
    public function __construct(UserProfileServiceInterface $userProfileService)
    {
        $this->_userProfileService = $userProfileService;
    }

    /**
     * Handle user login and return a JWT token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userProfile = $this->_userProfileService->fetchByEmail($request->input('email'));

        if (!$userProfile) {
            return response()->json([
                'message' => 'User profile not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user_profile' => new UserProfileResource($userProfile)
        ]);
    }
}
