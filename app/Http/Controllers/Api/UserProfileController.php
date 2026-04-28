<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;
use App\Services\Contracts\UserProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserProfileResource;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

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
     * @param RegistrationRequest $request
     * @return JsonResponse
     */
    public function store(RegistrationRequest $request): JsonResponse
    {
        $data = $request->only('name', 'email', 'password', 'bio', 'dob', 'gender');

        $userProfile = $this->_userProfileService->create($data);
        $token = JWTAuth::fromUser($userProfile->user);

        return response()->json([
            'data' => new UserProfileResource($userProfile),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ], Response::HTTP_CREATED);
    }
}
