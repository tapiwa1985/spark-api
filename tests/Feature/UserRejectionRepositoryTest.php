<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserRejection;
use App\Models\User;
use App\Repositories\Contracts\UserRejectionRepositoryInterface;

class UserRejectionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRejectionRepositoryInterface $_userRejectionRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_userRejectionRepository = app()->make(UserRejectionRepositoryInterface::class);
    }

    public function testCreateUserRejection()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $result = $this->_userRejectionRepository->create(['user_id' => $user->id, 'rejected_user_id' => $user2->id]);

        $this->assertDatabaseHas('user_rejections', ['user_id' => $user->id, 'rejected_user_id' => $user2->id]);

        $this->assertInstanceOf(UserRejection::class, $result);
        $this->assertEquals($result->user_id, $user->id);
        $this->assertEquals($result->rejected_user_id, $user2->id);
    }
}
