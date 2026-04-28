<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Http\Resources\UserResource;
use App\Http\Requests\LoginRequest;

/**
 * Controller responsible for handling user authentication, including login and token generation.
 * This controller uses JWT for authentication and relies on a user service to retrieve user information.
 * The login method validates the incoming request, attempts to authenticate the user, and returns a JSON response containing the JWT token and user information if successful. If authentication fails, it returns an unauthorized error response.
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    /**
     * The user service instance.
     * @var UserServiceInterface
     */
    private UserServiceInterface $_userService;

    /**
     * Create a new controller instance.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->_userService = $userService;
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

        $user = $this->_userService->getUserByEmail($request->input('email'));

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => new UserResource($user)
        ]);
    }
}
