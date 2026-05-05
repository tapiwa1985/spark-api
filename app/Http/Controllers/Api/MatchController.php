<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\UserMatchResource;
use App\Services\Contracts\MatchServiceInterface;
use App\Http\Resources\UserProfileResourceCollection;

/**
 * Exposes HTTP endpoints for the authenticated user’s mutual matches.
 *
 * Lists {@see \App\Models\UserProfile} rows for users who are matched with the current account.
 *
 * @package App\Http\Controllers\Api
 */
class MatchController extends Controller
{
    /**
     * Match domain service for listing matched profiles and performing match operations.
     *
     * @var MatchServiceInterface
     */
    private MatchServiceInterface $_matchService;

    /**
     * Inject the match service dependency.
     *
     * @param MatchServiceInterface $matchService Service resolving active matches to profile resources.
     */
    public function __construct(MatchServiceInterface $matchService)
    {
        $this->_matchService = $matchService;
    }

    /**
     * Returns every matched user’s profile for the authenticated user.
     *
     * Fetches all active matches where the authenticated user is either the initiator
     * or the recipient, and returns the corresponding user profiles as a collection.
     *
     * @return UserProfileResourceCollection Wrapped collection of {@see \App\Models\UserProfile} instances.
     */
    public function index(): UserProfileResourceCollection
    {
        $user = auth()->user();
        $matches = $this->_matchService->fetchMatchesForUser($user->id);

        return new UserProfileResourceCollection($matches);
    }

    /**
     * Display a specific match resource with its chat messages.
     *
     * Retrieves a single match by its ID, loads the related chat messages with their sender data,
     * and returns the match as a JSON resource.
     *
     * @param string $userMatchId The primary key of the match (cast to integer internally).
     * @return UserMatchResource A single match resource, including eager‑loaded chat messages and senders.
     */
    public function show(string $userMatchId): UserMatchResource
    {
        $match = $this->_matchService->find((int) $userMatchId);
        $match->loadMissing(['chatMessages.sender']);

        return new UserMatchResource($match);
    }

    /**
     * Unmatch (soft‑delete) a mutual match.
     *
     * Authorizes the action using the `unmatchUser` Gate policy, then delegates to the match service
     * to perform the unmatch operation (soft deletion). Returns a success JSON response.
     *
     * @param string $userMatchId The primary key of the match to delete.
     * @return JsonResponse HTTP 200 response with a confirmation message.
     */
    public function destroy(string $userMatchId): JsonResponse
    {
        $user = auth()->user();
        $userMatch = $this->_matchService->find((int) $userMatchId);
        Gate::authorize('unmatchUser', $userMatch);

        $this->_matchService->unmatch($user->id, (int)$userMatchId);

        return response()->json([
            'message' => 'Unmatched successfully!'
        ], JsonResponse::HTTP_OK);
    }
}
