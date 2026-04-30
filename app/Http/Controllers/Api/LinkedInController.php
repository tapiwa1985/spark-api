<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Http\Resources\UserProfileResource;

/**
 * Controller responsible for handling LinkedIn OAuth authentication.
 * This controller provides methods to initiate the LinkedIn OAuth flow and handle the callback from LinkedIn.
 * The getRedirectUrl method generates the LinkedIn OAuth authorization URL, while the callback method processes
 * the response from LinkedIn, retrieves the user's information, and returns it as a JSON response. The controller
 * relies on a user service to handle any user-related operations that may be necessary during the authentication process.
 *
 * @package App\Http\Controllers\Api
 */
class LinkedInController extends Controller
{
    /**
     * @var UserServiceInterface $userService
     */
    private UserServiceInterface $_userService;

    /**
     * @var UserProfileServiceInterface $userProfileService
     */
    private UserProfileServiceInterface $_userProfileService;
    /**
     * LinkedInController constructor.
     *
     * @param UserServiceInterface $userService
     * @param UserProfileServiceInterface $userProfileService
     */
    public function __construct(UserServiceInterface $userService, UserProfileServiceInterface $userProfileService)
    {
        $this->_userService = $userService;
        $this->_userProfileService = $userProfileService;
    }

    public function getRedirectUrl()
    {
        return Socialite::driver('linkedin-openid')
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }

    /**
     * @return JsonResponse
     */
    public function callback(Request $request): JsonResponse
    {
        $linkedInUser = Socialite::driver('linkedin-openid')
            ->stateless()
            ->user();

        $user = $this->_userService->fetchByLinkedInId($linkedInUser->id);

        if (!$user) {
            $user = $this->_userProfileService->create([
                'name' => $linkedInUser->name,
                'email' => $linkedInUser->email,
                'linkedin_id' => $linkedInUser->id,
                'linkedin_token' => $linkedInUser->token,
                'linkedin_refresh_token' => $linkedInUser->refreshToken ?? null,
            ]);
        }

        $userProfile = $user->profile;

        return response()->json([
            'data' => new UserProfileResource($userProfile),
            'token' => auth()->login($user),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
