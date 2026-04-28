<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepositoryInterface $_userRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_userRepository = app()->make(UserRepositoryInterface::class);
    }

     /**
     * Test the UserRepository's create method.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $userData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ];

        $result = $this->_userRepository->create($userData);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);
        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($result->name, $userData['name']);
        $this->assertEquals($result->email, $userData['email']);
    }

    /**
     * Test the UserRepository's find method.
     *
     * @return void
     */
    public function testGetUserById()
    {
        $user = User::factory()->create();

        $result = $this->_userRepository->find($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($result->id, $user->id);
        $this->assertEquals($result->name, $user->name);
    }
}
