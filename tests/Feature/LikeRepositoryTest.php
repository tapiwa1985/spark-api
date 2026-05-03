<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Like;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\LikeRepositoryInterface;

class LikeRepositoryTest extends TestCase
{
    /**
     * @var LikeRepositoryInterface
     */
    private LikeRepositoryInterface $_likeRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_likeRepository = app()->make(LikeRepositoryInterface::class);
    }

    public function testCreateLike()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $result = $this->_likeRepository->create([
            'user_id' => $user1->id,
            'liked_user_id' => $user2->id,
        ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user1->id,
            'liked_user_id' => $user2->id,
        ]);

        $this->assertInstanceOf(Like::class, $result);
        $this->assertEquals($result->user_id, $user1->id);
        $this->assertEquals($result->liked_user_id, $user2->id);
    }

    public function testGetUserReceivedLikes()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Like::factory()->create([
            'user_id' => $user2->id,
            'liked_user_id' => $user1->id,
        ]);

        $this->assertInstanceOf(Collection::class, $user1->receivedLikes);
        $this->assertCount(1, $user1->receivedLikes);
        $this->assertEquals($user1->id, $user1->receivedLikes->get(0)->liked_user_id);
        $this->assertEquals($user2->id, $user2->likes->get(0)->user_id);
    }
}
