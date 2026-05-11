<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery as m;
use App\Models\User;
use App\Models\UserMatch;
use App\Services\BlockedUserService;
use App\Repositories\Contracts\MatchRepositoryInterface;
use App\Repositories\Contracts\BlockedUserRepositoryInterface;

class BlockedUserServiceUnitTest extends TestCase
{
    public function testCreateBlockedUser()
    {
        $userId = 1;
        $blockedUserId = 2;

        $matchMock = m::mock(UserMatch::class)->makePartial();
        $matchMock->user_match_id = 1;
        $matchMock->user_id = $userId;
        $matchMock->matched_user_id = $blockedUserId;

        $matchMockCollection = collect([$matchMock]);

        $blockedUserRepoMock = m::mock(BlockedUserRepositoryInterface::class);
        $blockedUserRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => 1,
                'blocked_user_id' => 2
        ]);

        $matchRepoMock = m::mock(MatchRepositoryInterface::class);
        $matchRepoMock->shouldReceive('getMatchesForUser')
            ->once()
            ->with($userId)
            ->andReturn($matchMockCollection);

        $matchRepoMock->shouldReceive('update')
            ->once()
            ->with(1, ['status' => UserMatch::USER_MATCH_STATUS_BLOCKED]);

        
        $service = new BlockedUserService($blockedUserRepoMock, $matchRepoMock);

        $service->create(['user_id' => $userId, 'blocked_user_id' => $blockedUserId]);
    }
}
