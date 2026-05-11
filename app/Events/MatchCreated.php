<?php

namespace App\Events;

use App\Http\Resources\UserMatchResource;
use App\Models\UserMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public UserMatch $userMatch;

    public function __construct(UserMatch $userMatch)
    {
        $this->userMatch = $userMatch;
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userMatch->user_id),
            new PrivateChannel('user.' . $this->userMatch->matched_user_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'match.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $match = $this->userMatch->loadMissing('chatMessages.sender');

        return [
            'userMatch' => (new UserMatchResource($match))->resolve(),
        ];
    }
}
