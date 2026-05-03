<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Interest;
use App\Models\Like;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserMatch;
use Illuminate\Support\Collection;
use Mockery as m;
use App\Services\LikeService;
use App\Repositories\Contracts\LikeRepositoryInterface;
use App\Repositories\Contracts\MatchRepositoryInterface;

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
        $likeRepoMock->shouldReceive('findMutualLike')
            ->once()
            ->with($user2Mock->id, $user1Mock->id)
            ->andReturn(null);
        $likeRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $user2Mock->id,
                'liked_user_id' => $user1Mock->id,
            ])->andReturn($likeMock);

        $matchRepoMock = m::mock(MatchRepositoryInterface::class);

        $service = new LikeService($likeRepoMock, $matchRepoMock);

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

        $matchRepoMock = m::mock(MatchRepositoryInterface::class);

        $service = new LikeService($likeRepoMock, $matchRepoMock);

        $result = $service->getReceivedLikes($user1Mock->id);
        
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(UserProfile::class, $result->get(0));
        $this->assertEquals($user2ProfileMock->bio, $result->get(0)->bio);
    }

    public function testCreateLike_WhenMutualLikeExists_CreatesMatch(): void
    {
        // Arrange
        $user1Id = 1;
        $user2Id = 2;
        
        // Create mock for the existing like (User2 already liked User1)
        $existingLikeMock = m::mock(Like::class)->makePartial();
        $existingLikeMock->id = 100;
        $existingLikeMock->user_id = $user2Id;
        $existingLikeMock->liked_user_id = $user1Id;
        $existingLikeMock->matched_at = null;

        $likeRepoMock = m::mock(LikeRepositoryInterface::class);
        $matchRepoMock = m::mock(MatchRepositoryInterface::class);

        $likeRepoMock->shouldReceive('findMutualLike')
            ->once()
            ->with($user1Id, $user2Id)
            ->andReturn($existingLikeMock);

        $likeRepoMock->shouldReceive('update')
            ->once()
            ->withArgs(function (int $id, array $payload) use ($existingLikeMock) {
                if ($id !== $existingLikeMock->id) {
                    return false;
                }
                if (! isset($payload['matched_at'])) {
                    return false;
                }

                $at = $payload['matched_at'];

                return $at instanceof \DateTimeInterface;
            });

        $matchRepoMock->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $user1Id,
                'matched_user_id' => $user2Id,
            ])
            ->andReturn(m::mock(UserMatch::class));

        $likeService = new LikeService($likeRepoMock, $matchRepoMock);

        $result = $likeService->create(['user_id' => $user1Id, 'liked_user_id' => $user2Id]);

        $this->assertNotNull($result);
        $this->assertSame($existingLikeMock->id, $result->id);
    }
}
