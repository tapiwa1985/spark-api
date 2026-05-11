<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery as m;
use App\Models\User;
use App\Services\BlockedUserService;
use App\Repositories\Contracts\BlockedUserRepositoryInterface;

class BlockedUserServiceUnitTest extends TestCase
{
    public function testCreateBlockedUser()
    {
        $userId = 1;
        $blockedUserId = 2;

        $blockedUserRepoMock = m::mock(BlockedUserRepositoryInterface::class);
        $blockedUserRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => 1,
                'blocked_user_id' => 2
            ]);

        $service = new BlockedUserService($blockedUserRepoMock);

        $service->create(['user_id' => $userId, 'blocked_user_id' => $blockedUserId]);
    }
}
