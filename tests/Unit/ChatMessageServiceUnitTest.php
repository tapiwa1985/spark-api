<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery as m;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\UserMatch;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;
use App\Services\ChatMessageService;

class ChatMessageServiceUnitTest extends TestCase
{
    public function testCreateChatMessage()
    {
        $senderId = 1;
        $userMatchId = 1;
        $message = fake()->sentence();

        $request = [
            'message' => $message,
            'sender_id' => 1,
            'user_match_id' => 1,
        ];

        $chatMessageMock = m::mock(ChatMessage::class)->makePartial();
        $chatMessageMock->message = $message;
        $chatMessageMock->sender_id = 1;
        $chatMessageMock->user_match_id = 1;

        $chatMessageRepoMock = m::mock(ChatMessageRepositoryInterface::class);
        $chatMessageRepoMock->shouldReceive('create')
            ->once()
            ->with($request)
            ->andReturn($chatMessageMock);

        $chatMessageService = new ChatMessageService($chatMessageRepoMock);

        $result = $chatMessageService->create($request);

        $this->assertNotNull($result);
        $this->assertInstanceOf(ChatMessage::class, $result);
        $this->assertEquals($result->message, $message);
        $this->assertEquals($result->sender_id, $senderId);
        $this->assertEquals($result->user_match_id, $userMatchId);
    }

    public function testGetChatMessagesForMatch()
    {
        $message = fake()->sentence();

        $userMatchId = 1;
        $senderId = 1;

        $chatMessageMock = m::mock(ChatMessage::class)->makePartial();
        $chatMessageMock->message = $message;
        $chatMessageMock->sender_id = $senderId;
        $chatMessageMock->user_match_id = $userMatchId;

        $chatMessages = collect([$chatMessageMock]);

        $chatMessageRepo = m::mock(ChatMessageRepositoryInterface::class);
        $chatMessageRepo->shouldReceive('getMessagesForMatch')
            ->once()
            ->with($userMatchId)
            ->andReturn($chatMessages);

        $service = new ChatMessageService($chatMessageRepo);

        $result = $service->getMessagesForMatch($userMatchId);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(ChatMessage::class, $result->get(0));

        foreach($chatMessages as $chatMessage) {
            $this->assertTrue($result->contains($chatMessage));
        }
    }
}
