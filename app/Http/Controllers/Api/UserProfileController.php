<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Contracts\UserProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserProfileResource;

class UserProfileController extends Controller
{
    protected UserProfileServiceInterface $_userProfileService;

    public function __construct(UserProfileServiceInterface $userProfileService)
    {
        $this->_userProfileService = $userProfileService;
    }

    public function store(Request $request): JsonResponse
    {
       $data = $request->only('name', 'email', 'password', 'bio', 'dob', 'gender');

       $userProfile = $this->_userProfileService->create($data);

       return (new UserProfileResource($userProfile))
            ->response()
            ->setStatusCode(201);
    }
}
