<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;

/**
 * Feature tests for user repository behavior.
 */
class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepositoryInterface $_userRepository;

    /**
     * It resolves repository dependencies before each test.
     */
    public function setUp(): void 
    {
        parent::setUp();

        // Resolve the concrete repository through the container binding.
        $this->_userRepository = app()->make(UserRepositoryInterface::class);
    }

    /**
     * It creates and persists a user record via the repository.
     */
    public function testCreateUser()
    {
        // Arrange a valid user payload for repository persistence.
        $userData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ];

        // Act by calling the repository create method directly.
        $result = $this->_userRepository->create($userData);

        // Assert the row was persisted to the database.
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);
        
        // Assert the repository returns the expected model instance and values.
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($result->name, $userData['name']);
        $this->assertEquals($result->email, $userData['email']);
    }

    /**
     * It fetches a persisted user by identifier.
     */
    public function testGetUserById()
    {
        // Seed a user and fetch it through the repository contract.
        $user = User::factory()->create();

        $result = $this->_userRepository->find($user->id);

        // Verify the repository returns the correct model data.
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($result->id, $user->id);
        $this->assertEquals($result->name, $user->name);
    }
}
