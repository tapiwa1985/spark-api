<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    private UserServiceInterface $_userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->_userService = $userService;
    }

    public function login(Request $request): JsonResponse
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
