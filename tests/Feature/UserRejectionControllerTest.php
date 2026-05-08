<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserProfile;
use App\Models\UserRejection;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRejectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRejectUserAssertStatusOk()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-rejections',[
                'type' => UserRejection::USER_REJECTION_TYPE_SOFT,
                'rejected_user_id' => $userProfile2->user->id])
            ->assertOk();
        
        $this->assertDatabaseHas('user_rejections', [
            'user_id' => $userProfile->user->id,
            'rejected_user_id' => $userProfile2->user->id,
        ]);
    }

    public function testRejectUserWhenRejectedUserIdIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-rejections',[
                'type' => UserRejection::USER_REJECTION_TYPE_SOFT,
                'rejected_user_id' => ''])
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'rejected_user_id' => [
                        'The rejected user id field is required.'
                    ]
                ]
        ]);
    }

    public function testRejectUserWhenRejectedUserIdIsStringAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-rejections',[
                'type' => UserRejection::USER_REJECTION_TYPE_SOFT,
                'rejected_user_id' => 'invalid'])
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'rejected_user_id' => [
                        'The rejected user id field must be an integer.'
                    ]
                ]
        ]);
    }

    public function testRejectUserWhenRejectedUserIdIsNotValidAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-rejections',[
                'type' => UserRejection::USER_REJECTION_TYPE_SOFT,
                'rejected_user_id' => 99999999])
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'rejected_user_id' => [
                        'The selected rejected user id is invalid.'
                    ]
                ]
        ]);
    }

    public function testRejectUserWhenRejectedTypeIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-rejections',[
                'type' => '',
                'rejected_user_id' => $userProfile2->user->id])
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'type' => [
                        'The type field is required.'
                    ]
                ]
        ]);
    }

    public function testRejectUserWhenRejectedTypeIsNotValisAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-rejections',[
                'type' => 'invalid type',
                'rejected_user_id' => $userProfile2->user->id])
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'type' => [
                        'The selected type is invalid.'
                    ]
                ]
        ]);
    }
}
