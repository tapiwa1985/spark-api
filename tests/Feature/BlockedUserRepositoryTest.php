<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BlockedUser;
use App\Models\User;
use App\Repositories\Contracts\BlockedUserRepositoryInterface;

class BlockedUserRepositoryTest extends TestCase
{
    private BlockedUserRepositoryInterface $_blockedUserRepository;

    public function setUp(): void 
    {
        parent::setUp();
        $this->_blockedUserRepository = app()->make(BlockedUserRepositoryInterface::class);
    }

    public function testCreateBlockedUser()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $result = $this->_blockedUserRepository->create(['user_id' => $user->id, 'blocked_user_id' => $user2->id]);

        $this->assertDatabaseHas('blocked_users', [
            'user_id' => $user->id,
            'blocked_user_id' => $user2->id
        ]);

        $this->assertInstanceOf(BlockedUser::class, $result);
        $this->assertEquals($result->user_id, $user->id);
        $this->assertEquals($result->blocked_user_id, $user2->id);
    }
}
