<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\UserRejection;
use App\Http\Requests\UserRejectionRequest;
use App\Services\Contracts\UserRejectionServiceInterface;

/**
 * Handles API requests for user rejections.
 *
 * Processes rejection of users (soft or permanent) by the authenticated user,
 * using the user rejection service.
 *
 * @package App\Http\Controllers\Api
 */
class UserRejectionController extends Controller
{
    /**
     * The user rejection service instance.
     *
     * @var UserRejectionServiceInterface
     */
    private UserRejectionServiceInterface $_userRejectionService;

    /**
     * Create a new controller instance.
     *
     * @param UserRejectionServiceInterface $userRejectionService
     */
    public function __construct(UserRejectionServiceInterface $userRejectionService)
    {
        $this->_userRejectionService = $userRejectionService;
    }

    /**
     * Store a new user rejection.
     *
     * Creates a rejection record for the authenticated user against a rejected user.
     * If the type is "soft", the rejection expires after 30 days.
     *
     * @param UserRejectionRequest $request The validated request containing rejection data.
     * @return void
     */
    public function store(UserRejectionRequest $request): void
    {
        $expiresAt = null;
        $user = auth()->user();

        $data = $request->only('rejected_user_id');
        $data['user_id'] = $user->id;

        if ($request->get('type') == UserRejection::USER_REJECTION_TYPE_SOFT) {
            $expiresAt = Carbon::now()->addDays(30)->toDateTimeString();
        }

        $data['expires_at'] = $expiresAt;

        $this->_userRejectionService->create($data);
    }
}
