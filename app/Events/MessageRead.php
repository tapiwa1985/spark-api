<?php

namespace App\Events;

use App\Http\Resources\ChatMessageResource;
use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Persisted chat row that triggered the broadcast.
     */
    public ChatMessage $chatMessage;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chatMessage->user_match_id),
        ];
    }

    /**
     * Stable name for Pusher / Reverb clients (avoid FQCN backslash variants).
     */
    public function broadcastAs(): string
    {
        return 'message.read';
    }

    /**
     * Match {@see ChatMessageResource} so clients can parse the same shape as the HTTP API.
     */
    public function broadcastWith(): array
    {
        $message = $this->chatMessage->loadMissing('sender');

        return [
            'chatMessage' => (new ChatMessageResource($message))->resolve(),
        ];
    }
}
