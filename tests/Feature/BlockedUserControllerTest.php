<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserMatch;
use App\Models\UserProfile;
use Tymon\JWTAuth\Facades\JWTAuth;

class BlockedUserControllerTest extends TestCase
{
    use RefreshDatabase;
    public function testCreateBlockedUserAssertStatusOk()
    {
        $userProfile = UserProfile::factory()->create();
        $userProfile2 = UserProfile::factory()->create();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $userProfile->user->id,
            'matched_user_id' => $userProfile2->user->id,
        ]);

        $token = JWTAuth::fromUser($userProfile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/blocked-users', [
            'blocked_user_id' => $userProfile2->user->id,
        ])->assertStatus(200);

        $this->assertDatabaseHas('user_matches', [
            'id' => $userMatch->id,
            'status' => 'BLOCKED',
        ]);
    }

    public function testCreateBlockedUserWhenBlockedUserIdIsEmptyAssertStatusUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/blocked-users', [
            'blocked_user_id' => '',
        ])->assertStatus(422)
        ->assertJson([
            'errors' => [
                'blocked_user_id' => ['The blocked user id field is required.']
            ]
        ]);
    }

    public function testCreateBlockedUserWhenBlockedUserIdIsStringAssertStatusUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/blocked-users', [
            'blocked_user_id' => 'invalid',
        ])->assertStatus(422)
        ->assertJson([
            'errors' => [
                'blocked_user_id' => ['The blocked user id field must be an integer.']
            ]
        ]);
    }
}
