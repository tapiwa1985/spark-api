<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserProfile;
use App\Models\Like;
use Tymon\JWTAuth\Facades\JWTAuth;

class LikeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLikeUserAssertStatusOk()
    {
        $user1Profile = UserProfile::factory()->create();
        $user2Profile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($user2Profile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/likes', [
            'liked_user_id' => $user1Profile->user->id,
        ])
            ->assertStatus(200);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user2Profile->user->id,
            'liked_user_id' => $user1Profile->user->id,
            'matched_at' => null,
        ]);
    }

    public function testLikeUserWhenLikedUserIdIsEmptyAssertUnprocessable()
    {
        $user2Profile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($user2Profile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/likes', [
            'liked_user_id' => '',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'liked_user_id' => ['The liked user id field is required.']
                ]
            ]);
    }

    public function testLikeUserWhenLikedUserIdIsNotIntegerAssertStatusUnprocessable()
    {
        $user2Profile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($user2Profile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/likes', [
            'liked_user_id' => 'invalid-id',
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'liked_user_id' => ['The liked user id field must be an integer.']
                ]
            ]);
    }

    public function testLikeUserWhenLikedUserIdIsNotValidAssertStatusUnprocessable()
    {
        $user2Profile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($user2Profile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/likes', [
            'liked_user_id' => 80000,
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'liked_user_id' => ['The selected liked user id is invalid.']
                ]
            ]);
    }

    public function testLikeUserWhenUserIsAlreadyLikedAssertStatusUnprocessable()
    {
        $user1Profile = UserProfile::factory()->create();
        $user2Profile = UserProfile::factory()->create();
        Like::factory()->create([
            'user_id' => $user2Profile->user->id,
            'liked_user_id' => $user1Profile->user->id,
        ]);

        $token = JWTAuth::fromUser($user2Profile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/likes', [
            'liked_user_id' => $user1Profile->user->id,
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'liked_user_id' => ['You have already liked this user.']
                ]
            ]);
    }
}
