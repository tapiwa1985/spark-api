<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LikeUserRequest;
use App\Services\Contracts\LikeServiceInterface;

class LikeController extends Controller
{
    /**
     * @var LikeServiceInterface
     */
    private LikeServiceInterface $_likeService;

    /**
     * @param LikeServiceInterface
     */
    public function __construct(LikeServiceInterface $likeService)
    {
        $this->_likeService = $likeService;
    }

    /**
     * @param LikeUserRequest $request
     * @return JsonResponse
     */
    public function store(LikeUserRequest $request): JsonResponse
    {
        $data = $request->only('liked_user_id');
        $userProfile = auth()->user()->userProfile;
        $data['user_id'] = $userProfile->user->id;

        $like = $this->_likeService->create($data);

        return response()->json([]);
    }
}
