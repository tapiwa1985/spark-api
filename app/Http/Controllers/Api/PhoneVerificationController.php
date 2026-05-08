<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Utils\Contracts\PhoneNumberVerifierInterface;
use App\Http\Requests\SendVerificationCodeRequest;

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

    /**
     * Create a new controller instance.
     *
     * @param PhoneNumberVerifierInterface $phoneNumberVerifier
     */
    public function __construct(PhoneNumberVerifierInterface $phoneNumberVerifier)
    {
        $this->_phoneNumberVerifier = $phoneNumberVerifier;
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
}
