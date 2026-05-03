<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Like;
use App\Models\User;
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
}
