<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BlockUserRequest;
use App\Models\UserMatch;
use App\Services\Contracts\MatchServiceInterface;
use App\Services\Contracts\BlockedUserServiceInterface;
use Illuminate\Http\JsonResponse;

/**
 * Class BlockedUserController
 *
 * Handles HTTP requests related to blocking/unblocking users.
 * This controller orchestrates the blocking process, which includes:
 * - Creating a blocked user record via the BlockedUserService.
 * - Updating any existing matches between the blocker and the blocked user
 *   to a "blocked" status using the MatchService.
 *
 * All responses are returned in JSON format suitable for API clients.
 *
 * @package App\Http\Controllers\Api
 */
class BlockedUserController extends Controller
{
    /**
     * The service instance for managing blocked user business logic.
     *
     * @var BlockedUserServiceInterface
     */
    private BlockedUserServiceInterface $_blockedUserService;

    /**
     * The service instance for managing matches between users.
     *
     * @var MatchServiceInterface
     */
    private MatchServiceInterface $_matchService;

    /**
     * BlockedUserController constructor.
     *
     * Injects the required services via Laravel's service container.
     *
     * @param BlockedUserServiceInterface $blockedUserService Service for block operations.
     * @param MatchServiceInterface $matchService Service for match operations.
     */
    public function __construct(BlockedUserServiceInterface $blockedUserService, MatchServiceInterface $matchService)
    {
        $this->_blockedUserService = $blockedUserService;
        $this->_matchService = $matchService;
    }

    /**
     * Store a newly blocked user relationship.
     *
     * This method creates a block record for the currently authenticated user
     * against the user ID provided in the request. After creating the block,
     * it fetches all matches involving the authenticated user and updates any
     * match that includes the blocked user (either as the primary user or the
     * matched user) to the BLOCKED status.
     *
     * @param BlockUserRequest $request The validated request containing 'blocked_user_id'.
     * @return JsonResponse An empty JSON response with HTTP 200 OK on success.
     *
     * @throws \Illuminate\Auth\AuthenticationException If no user is authenticated.
     */
    public function store(BlockUserRequest $request): JsonResponse
    {
        $data = $request->only('blocked_user_id');
        $userId = auth()->user()->id;

        $data['user_id'] = $userId;

        $this->_blockedUserService->create($data);

        return response()->json([], JsonResponse::HTTP_OK);
    }
}
