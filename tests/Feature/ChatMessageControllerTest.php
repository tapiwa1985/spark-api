<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserMatch;
use Tymon\JWTAuth\Facades\JWTAuth;

class ChatMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSendChatMessage()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $message = fake()->sentence();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/chat-messages', [
            'message' => $message,
            'user_match_id' => $userMatch->id,
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'message' => $message,
                    'sender' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]
            ]);
    }

    public function testSendChatMessageWhenMessageIsEmptyAssertStatusUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $message = fake()->sentence();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/chat-messages', [
            'message' => '',
            'user_match_id' => $userMatch->id,
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'message' => ['The message field is required.']
                ]
            ]);
    }

    public function testSendChatMessageWhenUserMatchIdIsEmptyAssertStatusUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $message = fake()->sentence();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/chat-messages', [
            'message' => $message,
            'user_match_id' => ''
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'user_match_id' => ['The user match id field is required.']
                ]
            ]);
    }

    public function testSendChatMessageWhenUserMatchIdIsStringAssertStatusUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $message = fake()->sentence();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/chat-messages', [
            'message' => $message,
            'user_match_id' => 'invalid-id'
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'user_match_id' => ['The user match id field must be an integer.']
                ]
            ]);
    }

    public function testSendChatMessageWhenUserMatchIdDoesNotExistAssertStatusUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $message = fake()->sentence();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/chat-messages', [
            'message' => $message,
            'user_match_id' => 10000
        ])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'user_match_id' => ['The selected user match id is invalid.']
                ]
            ]);
    }
}
