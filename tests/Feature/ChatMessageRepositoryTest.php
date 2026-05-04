<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ChatMessage;
use App\Models\UserMatch;
use App\Models\User;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;

class ChatMessageRepositoryTest extends TestCase
{
    private ChatMessageRepositoryInterface $_chatMessageRepository;

    public function setUp(): void  
    {
        parent::setUp();

        $this->_chatMessageRepository = app()->make(ChatMessageRepositoryInterface::class);
    }

    public function testCreateChatMessage()
    {
        $message = fake()->sentence();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $match = UserMatch::factory()->create([
            'user_id' => $user1->id,
            'matched_user_id' => $user2->id,
        ]);

        $result = $this->_chatMessageRepository->create([
            'sender_id' => $user1->id,
            'message' => $message,
            'user_match_id' => $match->id,
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $user1->id,
            'message' => $message,
            'user_match_id' => $match->id,
        ]);

        $this->assertInstanceOf(ChatMessage::class, $result);
        $this->assertEquals($result->message, $message);
        $this->assertEquals($result->sender_id, $user1->id);
        $this->assertEquals($result->user_match_id, $match->id);
    }
}
