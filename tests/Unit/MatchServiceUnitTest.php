<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserMatch;
use App\Services\MatchService;
use Mockery as m;
use App\Repositories\Contracts\MatchRepositoryInterface;

class MatchServiceUnitTest extends TestCase
{
    public function testCreateUserMatch()
    {
        $user1Mock = m::mock(User::class)->makePartial();
        $user1Mock->id = 1;
        $user2Mock = m::mock(User::class)->makePartial();
        $user2Mock->id = 2;

        $matchMock = m::mock(UserMatch::class)->makePartial();
        $matchMock->user_id = 2;
        $matchMock->matched_user_id = 1;
        $matchMock->status = 'ACTIVE';

        $matchRepoMock = m::mock(MatchRepositoryInterface::class);
        $matchRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => 2,
                'matched_user_id' => 1,
            ])->andReturn($matchMock);

        $service = new MatchService($matchRepoMock);

        $result = $service->create(['user_id' => 2, 'matched_user_id' => 1]);

        $this->assertInstanceOf(UserMatch::class, $result);
        $this->assertEquals($result->user_id, $user2Mock->id);
        $this->assertEquals($result->matched_user_id, $user1Mock->id);
        $this->assertEquals($result->status, $matchMock->status);
    }
}
