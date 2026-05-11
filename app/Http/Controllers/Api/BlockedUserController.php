<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BlockUserRequest;
use App\Services\Contracts\BlockedUserServiceInterface;

class BlockedUserController extends Controller
{
    private BlockedUserServiceInterface $_blockedUserService;

    public function __construct(BlockedUserServiceInterface $blockedUserService)
    {
        $this->_blockedUserService = $blockedUserService;
    }

    public function store(BlockUserRequest $request): void
    {
        $data = $request->only('blocked_user_id');
        $userId = auth()->user()->id;

        $data['user_id'] = $userId;

        $this->_blockedUserService->create($data);
    }
}
