<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Http\Resources\UserProfileResource;
use App\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\RegistrationRequest;

/**
 * Password-based registration and login returning JWT access tokens and {@see UserProfileResource} payloads.
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * Creates profiles on register and resolves the profile bundle after password login.
     */
    private UserProfileServiceInterface $_userProfileService;

    /**
     * @param UserProfileServiceInterface $userProfileService Profile persistence and lookup by email.
     */
    public function __construct(UserProfileServiceInterface $userProfileService)
    {
        $this->_userProfileService = $userProfileService;
    }

    /**
     * Registers a user + minimal profile, issues a JWT via the `auth()` helper, returns `201` with token metadata.
     *
     * @return JsonResponse JSON containing `data` (profile resource), `token`, `token_type`, `expires_in`.
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        $data = $request->only(
            'name',
            'email',
            'password',
            'dob',
        );

        $userProfile = $this->_userProfileService->create($data);
        $token = auth()->login($userProfile->user);

        return response()->json([
            'data' => new UserProfileResource($userProfile),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], Response::HTTP_CREATED);
    }

    /**
     * Validates credentials; on success returns JWT plus {@see UserProfileResource}, or `401` / `404` when unusable.
     *
     * @return JsonResponse Token envelope with `user_profile`, or error payload.
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
