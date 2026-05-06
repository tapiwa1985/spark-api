<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\ChatMessage;
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

    public function testGetChatMessagesAssertStatusOk()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $token = JWTAuth::fromUser($user);

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessages = ChatMessage::factory(5)->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/chat-messages?match_id=' . $userMatch->id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'message',
                        'sender' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'read_at',
                    ]
                ]
        ]);

        $response->assertJsonCount(5, 'data');
    }

    public function testGetChatMessagesWhenMatchIdIsEmptyAssertUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $token = JWTAuth::fromUser($user);

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessages = ChatMessage::factory(5)->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/chat-messages?match_id=')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'match_id' => ['The match id field is required.']
                ]
        ]);
    }

    public function testGetChatMessagesWhenMatchIdIsStringAssertUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessages = ChatMessage::factory(5)->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/chat-messages?match_id=invalid')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'match_id' => ['The match id field must be an integer.']
                ]
        ]);
    }

    public function testGetChatMessagesWhenMatchIdIsNotValidAssertUnprocessable()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessages = ChatMessage::factory(5)->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/chat-messages?match_id=10000')
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'match_id' => ['The selected match id is invalid.']
                ]
        ]);
    }

    public function testGetChatMessagesWhenUserIsNotMatchedAssertForbidden()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser(User::factory()->create());

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessages = ChatMessage::factory(5)->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/chat-messages?match_id=' . $userMatch->id)
            ->assertStatus(403);
    }

    public function testUpdateReadStatusAssertStatusOk()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessage = ChatMessage::factory()->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/v1/chat-messages/' . $chatMessage->id);

        $response->assertOk();
        $readAt = $response->json('data.read_at');
        $this->assertNotNull($readAt);
        $this->assertNotSame('', $readAt);
    }

    public function testUpdateReadStatusWhenUserDoesNotOwnMatchAssertForbidden()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $token = JWTAuth::fromUser(User::factory()->create());

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user->id,
            'matched_user_id' =>  $user2->id,
        ]);

        $chatMessage = ChatMessage::factory()->create([
            'sender_id' => $user->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/v1/chat-messages/' . $chatMessage->id)
        ->assertForbidden();
    }
}
