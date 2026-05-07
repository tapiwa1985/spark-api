<?php

namespace App\Utils;

use Twilio\Rest\Client;
use Illuminate\Support\Str;

/**
 * Service for sending and verifying SMS verification codes using Twilio Verify.
 *
 * @package App\Utils
 */
class PhoneNumberVerifier implements PhoneNumberVerifierInterface
{
    /**
     * The Twilio REST API client instance.
     *
     * @var Client
     */
    private Client $_twilioClient;

    /**
     * Create a new phone number verifier instance.
     *
     * Initializes the Twilio client with credentials from the configuration.
     */
    public function __construct()
    {
        $this->_twilioClient = new Client(config('twilio.twilio_account_sid'), config('twilio.twilio_auth_token'));
    }

    /**
     * Send a verification code to the given phone number.
     *
     * @param string $phoneNumber The recipient's phone number in E.164 format.
     * @return string A unique UUID identifying this verification session.
     */
    public function sedCode(string $phoneNumber): string
    {
        $uuid = (string) Str::uuid();

        $this->_twilioClient->verify->v2->services(config('twilio.twilio_verify_sid'))
            ->verifications
            ->create($phoneNumber, 'sms');

        return $uuid;
    }

    /**
     * Verify that the provided code matches the one sent to the phone number.
     *
     * @param string $phoneNumber The recipient's phone number in E.164 format.
     * @param string $verificationCode The code entered by the user.
     * @return bool True if the verification code is approved, false otherwise.
     */
    public function verifyCode(string $phoneNumber, string $verificationCode): bool
    {
        $verificationCheck = $this->_twilioClient->verify->v2->services(config('twilio.twilio_verify_sid'))
            ->verificationChecks
            ->create([
                'to' => $phoneNumber,
                'code' => $verificationCode,
            ]);

        return $verificationCheck->status === 'approved';
    }
}
