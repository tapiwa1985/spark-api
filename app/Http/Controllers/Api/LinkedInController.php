<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Http\Resources\UserProfileResource;

/**
 * LinkedIn OpenID login: web redirect callback, optional mobile authorization-code exchange, user upsert, and JWT response.
 */
class LinkedInController extends Controller
{
    /**
     * Looks up users by LinkedIn id for returning visitors.
     */
    private UserServiceInterface $_userService;

    /**
     * Creates user + profile when no LinkedIn id exists yet.
     */
    private UserProfileServiceInterface $_userProfileService;

    /**
     * @param UserServiceInterface            $userService         LinkedIn id resolution.
     * @param UserProfileServiceInterface    $userProfileService Registration payload when linking a new account.
     */
    public function __construct(UserServiceInterface $userService, UserProfileServiceInterface $userProfileService)
    {
        $this->_userService = $userService;
        $this->_userProfileService = $userProfileService;
    }

    /**
     * @return string Authorization URL for the SPA or native client to open in a browser / web view.
     */
    public function getRedirectUrl()
    {
        return Socialite::driver('linkedin-openid')
            ->stateless()
            ->redirect()
            ->getTargetUrl();
    }

    /**
     * Web: Socialite session callback. Mobile: `POST` with `code`, `redirect_uri`, optional PKCE `code_verifier`.
     *
     * @return JsonResponse Profile + JWT on success, `422` when token or profile fetch fails.
     */
    public function callback(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            return $this->callbackFromAuthorizationCodePayload($request);
        }

        $linkedInUser = Socialite::driver('linkedin-openid')
            ->stateless()
            ->user();

        return $this->loginOrRegisterWithLinkedIn(
            linkedinId: (string) $linkedInUser->id,
            name: (string) ($linkedInUser->name ?? ''),
            email: (string) ($linkedInUser->email ?? ''),
            accessToken: (string) $linkedInUser->token,
            refreshToken: $linkedInUser->refreshToken ?? null,
        );
    }

    /**
     * Mobile/native OAuth via react-native-app-auth: frontend sends authorization code plus the
     * redirect_uri issued to LinkedIn (must match authorization request and token exchange).
     */
    private function callbackFromAuthorizationCodePayload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'redirect_uri' => ['required', 'string'],
            'code_verifier' => ['nullable', 'string'],
        ]);

        $params = [
            'grant_type' => 'authorization_code',
            'code' => $validated['code'],
            'redirect_uri' => $validated['redirect_uri'],
            'client_id' => config('services.linkedin-openid.client_id'),
            'client_secret' => config('services.linkedin-openid.client_secret'),
        ];

        if (! empty($validated['code_verifier'])) {
            $params['code_verifier'] = $validated['code_verifier'];
        }

        $tokenResp = Http::asForm()->timeout(30)->post('https://www.linkedin.com/oauth/v2/accessToken', $params);

        if (! $tokenResp->successful()) {
            Log::warning('linkedin_token_exchange_failed', [
                'status' => $tokenResp->status(),
                'body' => $tokenResp->body(),
            ]);

            return response()->json([
                'message' => 'Could not exchange LinkedIn authorization code.',
            ], 422);
        }

        $accessToken = (string) data_get($tokenResp->json(), 'access_token');
        $refreshToken = data_get($tokenResp->json(), 'refresh_token');

        $profileResp = Http::withToken($accessToken)
            ->acceptJson()
            ->timeout(30)
            ->get('https://api.linkedin.com/v2/userinfo');

        if (! $profileResp->successful()) {
            Log::warning('linkedin_userinfo_failed', [
                'status' => $profileResp->status(),
                'body' => $profileResp->body(),
            ]);

            return response()->json([
                'message' => 'Could not fetch LinkedIn profile.',
            ], 422);
        }

        $payload = $profileResp->json();
        $sub = (string) data_get($payload, 'sub', '');

        if ($sub === '') {
            return response()->json([
                'message' => 'Invalid LinkedIn profile response.',
            ], 422);
        }

        return $this->loginOrRegisterWithLinkedIn(
            linkedinId: $sub,
            name: (string) data_get($payload, 'name', ''),
            email: (string) data_get($payload, 'email', ''),
            accessToken: $accessToken,
            refreshToken: is_string($refreshToken) ? $refreshToken : null,
        );
    }

    /**
     * Finds an existing user by LinkedIn id or creates one via {@see UserProfileServiceInterface::create}, then returns JWT JSON.
     *
     * @return JsonResponse Same shape as password login: `data`, `token`, `token_type`, `expires_in`.
     */
    private function loginOrRegisterWithLinkedIn(
        string $linkedinId,
        string $name,
        string $email,
        string $accessToken,
        ?string $refreshToken,
    ): JsonResponse {
        $user = $this->_userService->fetchByLinkedInId($linkedinId);
        $userProfile = null;

        if (! $user) {
            $userProfile = $this->_userProfileService->create([
                'name' => $name,
                'email' => $email,
                'linkedin_id' => $linkedinId,
                'linkedin_token' => $accessToken,
                'linkedin_refresh_token' => $refreshToken,
            ]);
            $user = $userProfile->user;
        }

        return response()->json([
            'data' => new UserProfileResource($userProfile ?? $user->userProfile),
            'token' => auth()->login($user),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
