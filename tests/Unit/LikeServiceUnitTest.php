<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Interest;
use App\Models\Like;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Mockery as m;
use App\Services\LikeService;
use App\Repositories\Contracts\LikeRepositoryInterface;

class LikeServiceUnitTest extends TestCase
{
    public function testCreateLike()
    {
        $user1Mock = m::mock(User::class)->makePartial();
        $user1Mock->id = 1;
        $user2Mock = m::mock(User::class)->makePartial();
        $user2Mock->id = 2;

        $likeMock = m::mock(Like::class)->makePartial();
        $likeMock->user_id = $user2Mock->id;
        $likeMock->liked_user_id = $user1Mock->id;
        $likeMock->matched_at = null;

        $likeRepoMock = m::mock(LikeRepositoryInterface::class);
        $likeRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $user2Mock->id,
                'liked_user_id' => $user1Mock->id,
            ])->andReturn($likeMock);
        
        $service = new LikeService($likeRepoMock);

        $result = $service->create([
            'user_id' => $user2Mock->id,
            'liked_user_id' => $user1Mock->id
        ]);

        $this->assertNotNull($result);
        $this->assertEquals($result->user_id, $user2Mock->id);
        $this->assertEquals($result->liked_user_id, $user1Mock->id);
        $this->assertNull($result->matched_at);
        $this->assertInstanceOf(Like::class, $result);
    }

    public function testGetListOfReceivedLikes()
    {
        $user1Mock = m::mock(User::class)->makePartial();
        $user1Mock->id = 1;

        $interestMock = m::mock(Interest::class)->makePartial();
        $interestMock->interest_name = fake()->word();

        $user2Mock = m::mock(User::class)->makePartial();
        $user2Mock->name = fake()->name();
        $user2Mock->id = 2;

        $user2ProfileMock = m::mock(UserProfile::class)->makePartial();
        $user2ProfileMock->dob = fake()->date();
        $user2ProfileMock->bio = fake()->sentence();
        $user2ProfileMock->user = $user2Mock;

        $user2ProfileMock->interests = collect([$interestMock]);

        $expected = collect([$user2ProfileMock]);

        $likeRepoMock = m::mock(LikeRepositoryInterface::class);
        $likeRepoMock->shouldReceive('getReceivedLikes')
            ->once()
            ->with($user1Mock->id)
            ->andReturn($expected);

        $service = new LikeService($likeRepoMock);

        $result = $service->getReceivedLikes($user1Mock->id);
        
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(UserProfile::class, $result->get(0));
        $this->assertEquals($user2ProfileMock->bio, $result->get(0)->bio);
    }
}
