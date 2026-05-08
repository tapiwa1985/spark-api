<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CuratedMatchResourceCollection;
use App\Services\Contracts\CuratedMatchServiceInterface;

/**
 * Handles API requests for curated matches.
 *
 * Provides endpoints to retrieve curated matches for the authenticated user.
 */
class CuratedMatchController extends Controller
{
    private CuratedMatchServiceInterface $curatedMatchService;

    /**
     * Create a new controller instance.
     *
     * @param CuratedMatchServiceInterface $curatedMatchService Service to fetch curated matches.
     */
    public function __construct(CuratedMatchServiceInterface $curatedMatchService)
    {
        $this->curatedMatchService = $curatedMatchService;
    }

    /**
     * Get the list of active curated matches for the authenticated user.
     *
     * @return CuratedMatchResourceCollection A collection of curated match resources.
     */
    public function index(): CuratedMatchResourceCollection
    {
        $user = auth()->user();
        $matches = $this->curatedMatchService->fetchActiveCuratedMatchesForUser((int) $user->id);

        return new CuratedMatchResourceCollection($matches);
    }
}
