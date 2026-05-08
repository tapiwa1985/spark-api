<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Mockery as m;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Utils\Contracts\PhoneNumberVerifierInterface;

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

    public function testVerifyCodeWhenPhoneIsEmptyAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '',
            'verification_code' => '1234'
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/verify-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'mobile_phone' => ['The mobile phone field is required.']
                ]
            ]);
    }

    public function testVerifyCodeWhenPhoneIsNotValidAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '12345',
            'verification_code' => '1234'
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/verify-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'mobile_phone' => ['validation.phone']
                ]
            ]);
    }

    public function testVerifyCodeWhenVerificationCodeIsEmptyAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '+27638524252',
            'verification_code' => ''
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/verify-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'verification_code' => ['The verification code field is required.']
                ]
            ]);
    }

    public function testVerifyCodeWhenVerificationCodeIsLessThanFourDigitsAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '+27638524252',
            'verification_code' => '12'
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/verify-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'verification_code' => ['The verification code field must be at least 4 characters.']
                ]
            ]);
    }

    public function testVerifyCodeWhenVerificationCodeIsMoreThanFourDigitsAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'mobile_phone' => '+27638524252',
            'verification_code' => '1244556'
        ];

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/verify-otp', $request)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'verification_code' => ['The verification code field must not be greater than 4 characters.']
                ]
            ]);
    }

    public function testVerifyMobileNumber()
    {
        $user = User::factory()->create();

        $phoneVerifierMock = m::mock(PhoneNumberVerifierInterface::class);
        $phoneVerifierMock->shouldReceive('verifyCode')
            ->once()
            ->with('+27638524252', '1234')
            ->andReturn(true);

        $request = [
            'mobile_phone' => '+27638524252',
            'verification_code' => '1234'
        ];

        $this->app->instance(PhoneNumberVerifierInterface::class, $phoneVerifierMock);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/phone-verification/verify-otp', $request)
        ->assertOk()
        ->assertJson([
            'data' => [
                'message' => 'Phone number verification successful!'
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'mobile_phone' => '+27638524252',
        ]);
    }
}
