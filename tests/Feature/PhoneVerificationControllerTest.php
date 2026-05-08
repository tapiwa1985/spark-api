<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class PhoneVerificationControllerTest extends TestCase
{
    public function testSendVerificationCodeWhenMobileIsNotValidAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '123456',
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/send-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'mobile_phone' => ['validation.phone']
                ]
            ]);
    }

    public function testSendVerificationCodeWhenMobileIsEmptyAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '',
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/send-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'mobile_phone' => ['The mobile phone field is required.']
                ]
            ]);
    }
}
