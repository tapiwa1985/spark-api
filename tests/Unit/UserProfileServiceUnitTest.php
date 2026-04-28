<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\UserProfileService;

/**
 * Unit tests for user profile service business logic.
 */
class UserProfileServiceUnitTest extends TestCase
{
    /**
     * It orchestrates user and profile creation through repositories.
     */
    public function testCreateUserProfile()
    {
        // Arrange base user attributes expected by UserRepository::create.
        $mockUserData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ];

        // Build a partial user model returned by the mocked user repository.
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->id = 1;
        $mockUser->name = $mockUserData['name'];
        $mockUser->email = $mockUserData['email'];
        $mockUser->password = $mockUserData['password'];

        // Arrange profile attributes expected by UserProfileRepository::create.
        $mockUserProfileData = [
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Build a partial profile model returned by the mocked profile repository.
        $mockUserProfile = Mockery::mock(UserProfile::class)->makePartial();
        $mockUserProfile->user = $mockUser;
        $mockUserProfile->bio = $mockUserProfileData['bio'];
        $mockUserProfile->dob = $mockUserProfileData['dob'];
        $mockUserProfile->gender = $mockUserProfileData['gender'];

        // Expect one user creation call with exactly the arranged user payload.
        $userRepoMock = $this->mock(
            UserRepositoryInterface::class,
            function ($mock) use ($mockUserData, $mockUser) {
                $mock->shouldReceive('create')
                ->once()
                ->with($mockUserData)
                ->andReturn($mockUser);
            }
        );

        // Expect one profile creation call that uses the created user id.
        $userProfileRepoMock = $this->mock(
            UserProfileRepositoryInterface::class,
            function ($mock) use ($mockUserProfileData, $mockUserProfile, $mockUser) {
                $mock->shouldReceive('create')
                ->once()
                ->with([
                    'user_id' => $mockUser->id,
                    'bio' => $mockUserProfileData['bio'],
                    'dob' => $mockUserProfileData['dob'],
                    'gender' => $mockUserProfileData['gender'],
                ])
                ->andReturn($mockUserProfile);
            }
        );

        // Act: instantiate the service and create a profile through it.
        $service = new UserProfileService($userRepoMock, $userProfileRepoMock);

        $result = $service->create(array_merge($mockUserData, $mockUserProfileData));

        // Assert the service returns the expected profile and attached user data.
        $this->assertNotNull($result);
        $this->assertInstanceOf(UserProfile::class, $result);
        $this->assertEquals($result->bio, $mockUserProfileData['bio']);
        $this->assertEquals($result->dob, $mockUserProfileData['dob']);
        $this->assertEquals($result->gender, $mockUserProfileData['gender']);
        $this->assertInstanceOf(User::class, $result->user);
        $this->assertEquals($result->user->name, $mockUserData['name']);
        $this->assertEquals($result->user->email, $mockUserData['email']);
    }
}
