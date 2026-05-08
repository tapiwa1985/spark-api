<?php

namespace App\Utils\Contracts;

/**
 * Interface for phone number verification via SMS.
 *
 * Defines the contract for sending and verifying verification codes
 * using a service like Twilio Verify.
 *
 * @package App\Utils\Contracts
 */
interface PhoneNumberVerifierInterface
{
    /**
     * Send a verification code to the specified phone number.
     *
     * @param string $phoneNumber The phone number to send the code to (in E.164 format).
     * @return string A unique identifier for this verification session (e.g., UUID).
     */
    public function sendCode(string $phoneNumber): string;

    /**
     * Verify that the provided code matches the one sent to the phone number.
     *
     * @param string $phoneNumber The phone number being verified.
     * @param string $verificationCode The code entered by the user.
     * @return bool True if the code is valid and approved, false otherwise.
     */
    public function verifyCode(string $phoneNumber, string $verificationCode): bool;
}
