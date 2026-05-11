<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserProfile;
use App\Models\Like;
use App\Models\Language;
use App\Models\Interest;
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

    public function testLikeUserWhenMutualLikeExistsAssertMatchCreated()
    {
        $user1Profile = UserProfile::factory()->create();
        $user2Profile = UserProfile::factory()->create();
        $like = Like::factory()->create([
            'user_id' => $user2Profile->user->id,
            'liked_user_id' => $user1Profile->user->id,
        ]);

        $token = JWTAuth::fromUser($user1Profile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/likes', [
            'liked_user_id' => $user2Profile->user->id,
        ])
            ->assertStatus(200);

        $this->assertDatabaseHas('user_matches', [
            'user_id' => $user1Profile->user->id,
            'matched_user_id' => $user2Profile->user->id,
        ]);
    }

    public function testGetLikesReceivedAssertStatusOk()
    {
        $user1Profile = UserProfile::factory()->create();
        $userProfiles = UserProfile::factory(5)->create();
        $languages = Language::factory(3)->create();
        $interests = Interest::factory(3)->create();

        foreach ($userProfiles as $userProfile) {
            $userProfile->languages()->attach($languages->pluck('id'));
            $userProfile->interests()->attach($interests->pluck('id'));
            Like::factory()->create([
                'user_id' => $userProfile->user->id,
                'liked_user_id' => $user1Profile->user->id,
            ]);
        }

        $token = JWTAuth::fromUser($user1Profile->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/likes')
        ->assertStatus(200);

        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'bio',
                    'dob',
                    'gender',
                    'job_title',
                    'industry' => [
                        'id',
                        'industry_name',
                    ],
                    'interests' => [
                        '*' => [
                            'id',
                            'interest_name',
                        ],
                    ],
                    'languages' => [
                        '*' => [
                            'id',
                            'language_name',
                        ],
                    ],
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ],
        ]);
    }
}
