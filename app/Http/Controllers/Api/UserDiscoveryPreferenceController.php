<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserDiscoveryPreferenceResource;
use App\Http\Requests\CreateUserDiscoveryPreferenceRequest;
use App\Services\Contracts\UserDiscoveryPreferenceServiceInterface;

/**
 * Handles HTTP requests for user discovery preferences.
 *
 * @package App\Http\Controllers\Api
 */
class UserDiscoveryPreferenceController extends Controller
{
    /**
     * The user discovery preference service instance.
     *
     * @var UserDiscoveryPreferenceServiceInterface
     */
    private UserDiscoveryPreferenceServiceInterface $_userDiscoveryPreferenceService;

    /**
     * Create a new controller instance.
     *
     * @param UserDiscoveryPreferenceServiceInterface $userDiscoveryPreferenceService
     */
    public function __construct(UserDiscoveryPreferenceServiceInterface $userDiscoveryPreferenceService)
    {
        $this->_userDiscoveryPreferenceService = $userDiscoveryPreferenceService;
    }

    /**
     * Store a newly created user discovery preference.
     *
     * @param CreateUserDiscoveryPreferenceRequest $request The validated request containing preference data.
     * @return UserDiscoveryPreferenceResource The created preference as a resource.
     */
    public function store(CreateUserDiscoveryPreferenceRequest $request): UserDiscoveryPreferenceResource
    {
        $data = $request->only(
            'min_age',
            'max_age',
            'max_distance_radius_km',
            'gender',
            'verified_only',
            'interestIds',
            'languageIds',
            'industryIds'
        );

        $data['user_id'] = auth()->user()->id;

        $userDiscoveryPref = $this->_userDiscoveryPreferenceService->create($data);

        return new UserDiscoveryPreferenceResource($userDiscoveryPref);
    }
}
