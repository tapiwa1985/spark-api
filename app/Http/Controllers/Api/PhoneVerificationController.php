<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Utils\Contracts\PhoneNumberVerifierInterface;
use App\Http\Requests\SendVerificationCodeRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Http\Requests\VerifyCodeRequest;

/**
 * Handles API requests for phone verification.
 *
 * Provides an endpoint to send a verification code to a user's mobile number
 * using the phone number verifier service.
 *
 * @package App\Http\Controllers\Api
 */
class PhoneVerificationController extends Controller
{
    /**
     * The phone number verifier service instance.
     *
     * @var PhoneNumberVerifierInterface
     */
    private PhoneNumberVerifierInterface $_phoneNumberVerifier;

    private UserServiceInterface $_userService;

    /**
     * Create a new controller instance.
     *
     * @param PhoneNumberVerifierInterface $phoneNumberVerifier
     */
    public function __construct(PhoneNumberVerifierInterface $phoneNumberVerifier, UserServiceInterface $userService)
    {
        $this->_phoneNumberVerifier = $phoneNumberVerifier;
        $this->_userService = $userService;
    }

    /**
     * Send a verification code to the specified mobile number.
     *
     * Validates the request, delegates code sending to the verifier service,
     * and returns a unique identifier (UUID) associated with the verification attempt.
     *
     * @param SendVerificationCodeRequest $request The validated request containing the mobile number.
     * @return JsonResponse JSON response containing the verification UUID.
     */
    public function sendVerificationCode(SendVerificationCodeRequest $request): JsonResponse
    {
        $mobileNumber = $request->input('mobile_phone');

        $uuid = $this->_phoneNumberVerifier->sendCode($mobileNumber);

        return response()->json(['uuid' => $uuid]);
    }

    /**
     * Verify a user's mobile phone number using OTP verification.
     *
     * This method validates the provided verification code against the mobile number,
     * and if successful, updates the user's record with the verified mobile number
     * and timestamp.
     *
     * @param VerifyCodeRequest $request The request object containing mobile_phone and verification_code
     *
     * @return \Illuminate\Http\JsonResponse Returns success message if verification passes,
     * @throws \Illuminate\Auth\Access\AuthorizationException If user is not authenticated
     */
    public function verifyCode(VerifyCodeRequest $request)
    {
        $user = auth()->user();

        $mobileNumber = $request->input('mobile_phone');
        $code = $request->input('verification_code');

        if ($this->_phoneNumberVerifier->verifyCode($mobileNumber, $code)) {
            $this->_userService->update((int)$user->id, [
                'mobile_phone' => $mobileNumber,
                'mobile_phone_verified_at' => now()
            ]);

            return response()->json([
                'data' => [
                    'message' => 'Phone number verification successful!'
                ]
            ]);
        }

        return response()->json([
            'errors' => [
                'verification_code' => ['Invalid verification code provided.']
            ]
        ], 400);
    }
}
