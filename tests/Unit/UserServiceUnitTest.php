<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserServiceUnitTest extends TestCase
{
    /**
     * Test the getUserByEmail method of UserService
     *
     * @return void
     */
    public function testGetUserByEmail()
    {
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->name = fake()->name();
        $mockUser->email = fake()->email();

        $userRepoMock = Mockery::mock(UserRepositoryInterface::class);
        $userRepoMock->shouldReceive('findByEmail')
            ->with($mockUser->email)
            ->once()
            ->andReturn($mockUser);

        $userService = new UserService($userRepoMock);
        $result = $userService->getUserByEmail($mockUser->email);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($mockUser->email, $result->email);
        $this->assertEquals($mockUser->name, $result->name);
    }
}
