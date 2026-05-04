<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserProfile;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\ChatMessage;
use Tymon\JWTAuth\Facades\JWTAuth;

class MatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetUserMatchesAssertStatusOk()
    {
        $user = User::factory()->create();
        $userProfiles = UserProfile::factory(10)->create();

        foreach($userProfiles as $userProfile) {
            UserMatch::factory()->create([
                'user_id' => $user->id,
                'matched_user_id' => $userProfile->user->id,
            ]);
        }

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/matches')
            ->assertStatus(200);

        $response->assertJsonCount(10, 'data');
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
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                    ],
                ],
            ]);
    }

    public function testGetUserMatchAssertStatusOk()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' => $user2->id,
        ]);

        $chatMessage = ChatMessage::factory()->create([
            'user_match_id' => $userMatch->id,
            'sender_id' => $user2->id,
            'message' => fake()->sentence(),
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/matches/' . $userMatch->id)
            ->assertOk();

        $response->assertJsonPath('data.id', $userMatch->id);
        $response->assertJsonPath('data.chatMessages.0.id', $chatMessage->id);
        $response->assertJsonPath('data.chatMessages.0.message', $chatMessage->message);
        $response->assertJsonPath('data.chatMessages.0.sender.id', $user2->id);
    }
}
