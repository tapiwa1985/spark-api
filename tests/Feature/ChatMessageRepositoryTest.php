<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ChatMessage;
use App\Models\UserMatch;
use App\Models\User;
use Illuminate\Support\Collection;
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

    public function testGetListOfChatMessagesForMatch()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $userMatch = UserMatch::factory()->create([
            'user_id' => $user1->id,
            'matched_user_id' => $user2->id,
        ]);

        $chatMessages = ChatMessage::factory(5)->create([
            'sender_id' => $user1->id,
            'message' => fake()->sentence(),
            'user_match_id' => $userMatch->id,
        ]);

        $result = $this->_chatMessageRepository->getMessagesForMatch($userMatch->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(5, $result);
        $this->assertInstanceOf(ChatMessage::class, $result->get(0));

        foreach ($chatMessages as $chatMessage) {
            $this->assertTrue($result->contains($chatMessage));
        }
    }

    public function testUpdateReadStatus()
    {
        $chatMessage = ChatMessage::factory()->create();

        $result = $this->_chatMessageRepository->update($chatMessage->id, ['read_at' => now()]);

        $this->assertInstanceOf(ChatMessage::class, $result);
        $this->assertEquals($chatMessage->id, $result->id);
        $this->assertEquals($chatMessage->sender_id, $result->sender_id);
        $this->assertEquals($result->message, $chatMessage->message);
        $this->assertNotNull($result->read_at);
    }
}
