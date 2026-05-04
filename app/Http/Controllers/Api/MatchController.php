<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * Match domain service for listing matched profiles.
     *
     * @var MatchServiceInterface
     */
    private MatchServiceInterface $_matchService;

    /**
     * @param MatchServiceInterface $matchService Service resolving active matches to profile resources.
     *
     * @return void
     */
    public function __construct(MatchServiceInterface $matchService)
    {
        $this->_matchService = $matchService;
    }

    /**
     * Returns every matched user’s profile for the authenticated user.
     *
     * @return UserProfileResourceCollection Wrapped collection of {@see \App\Models\UserProfile} instances.
     */
    public function index(): UserProfileResourceCollection
    {
        $user = auth()->user();
        $matches = $this->_matchService->fetchMatchesForUser($user->id);

        return new UserProfileResourceCollection($matches);
    }
}
