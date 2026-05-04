<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\UserProfile;
use App\Services\MatchService;
use Mockery as m;
use App\Repositories\Contracts\MatchRepositoryInterface;

/**
 * Unit tests for {@see MatchService} delegating to {@see MatchRepositoryInterface}.
 */
class MatchServiceUnitTest extends TestCase
{
    /**
     * Ensures {@see MatchService::create} persists a pair of user ids and returns the resulting match model.
     *
     * @return void
     */
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

    /**
     * Ensures {@see MatchService::fetchMatchesForUser} returns the repository collection of match profiles.
     *
     * @return void
     */
    public function testGetListOfMatches()
    {
        $userMock = m::mock(User::class)->makePartial();
        $userMock->id = 1;

        $match1Mock = m::mock(UserProfile::class)->makePartial();
        $match1Mock->id = 1;
        $match1Mock->bio = fake()->sentence();

        $matchesList = collect([$match1Mock]);

        $matchRepoMock = m::mock(MatchRepositoryInterface::class);
        $matchRepoMock->shouldReceive('getMatchesForUser')
            ->once()
            ->with($userMock->id)
            ->andReturn($matchesList);
        
        $service = new MatchService($matchRepoMock);

        $result = $service->fetchMatchesForUser($userMock->id);

        $this->assertNotNull($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(UserProfile::class, $result->get(0));
    }
}
