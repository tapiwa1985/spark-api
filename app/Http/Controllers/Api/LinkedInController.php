<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;
use App\Http\Controllers\Controller;

class LinkedInController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('linkedin-openid')->redirect();
    }

    /**
     * @return JsonResponse
     */
    public function callback(): JsonResponse
    {
        $linkedInUser = Socialite::driver('linkedin-openid')->user();

        return response()->json([
            'name' => $linkedInUser->name,
            'email' => $linkedInUser->email,
        ]);
    }
}
