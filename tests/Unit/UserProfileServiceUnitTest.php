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

        $mockIndustry = Mockery::mock(Industry::class)->makePartial();
        $mockIndustry->id = rand(100, 900);
        $mockIndustry->industry_name = fake()->word();

        // Arrange profile attributes expected by UserProfileRepository::create.
        $mockUserProfileData = [
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
            'job_title' => fake()->jobTitle(),
            'industry_id' => $mockIndustry->id,
        ];

        // Build a partial profile model returned by the mocked profile repository.
        $mockUserProfile = Mockery::mock(UserProfile::class)->makePartial();
        $mockUserProfile->user = $mockUser;
        $mockUserProfile->bio = $mockUserProfileData['bio'];
        $mockUserProfile->dob = $mockUserProfileData['dob'];
        $mockUserProfile->gender = $mockUserProfileData['gender'];
        $mockUserProfile->job_title = $mockUserProfileData['job_title'];
        $mockUserProfile->industry_id = $mockUserProfileData['industry_id'];

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
                    'industry_id' => $mockUserProfileData['industry_id'],
                    'job_title' => $mockUserProfileData['job_title'],
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
        $this->assertEquals($result->job_title, $mockUserProfileData['job_title']);
        $this->assertEquals($result->industry_id, $mockUserProfileData['industry_id']);
    }

    public function testGetUserProfileByEmail()
    {
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->name = fake()->name();
        $mockUser->email = fake()->email();
        $mockUser->id = 1;

        $userRepoMock = Mockery::mock(UserRepositoryInterface::class);

        $mockUserProfile = Mockery::mock(UserProfile::class)->makePartial();
        $mockUserProfile->user = $mockUser;
        $mockUserProfile->bio = fake()->sentence();
        $mockUserProfile->dob = fake()->date();
        $mockUserProfile->gender = 'male';

        $userProfileRepoMock = $this->mock(UserProfileRepositoryInterface::class, 
            function($mock) use($mockUser, $mockUserProfile) {
                $mock->shouldReceive('findByEmail')
                ->once()
                ->with($mockUser->email)
                ->andReturn($mockUserProfile);
            });

        $service = new UserProfileService($userRepoMock, $userProfileRepoMock);

        $result = $service->fetchByEmail($mockUser->email);

        $this->assertNotNull($result);
        $this->assertInstanceOf(UserProfile::class, $result);
        $this->assertInstanceOf(User::class, $result->user);

        $this->assertEquals($result->bio, $mockUserProfile->bio);
        $this->assertEquals($result->dob, $mockUserProfile->dob);
        $this->assertEquals($result->gender, $mockUserProfile->gender);
        $this->assertEquals($result->job_title, $mockUserProfile->job_title);
        $this->assertEquals($result->industry_id, $mockUserProfile->industry_id);

        $this->assertEquals($result->user->id, $mockUser->id);
        $this->assertEquals($result->user->email, $mockUser->email);
        $this->assertEquals($result->user->name, $mockUser->name);
    }

    public function testUpdateUserProfile()
    {
        $mockUser = $this->createMockUser();

        $userRepoMock = $this->mock(UserRepositoryInterface::class);
        $mockUserProfile = $this->createMockUserProfile($mockUser);

        $mockUserProfileData = [
            'bio' => fake()->paragraph,
            'dob' => fake()->date(),
            'gender' => 'male'
        ];

        $expected = Mockery::mock(UserProfile::class)->makePartial();
        $expected->user = $mockUser;
        $expected->bio = $mockUserProfileData['bio'];
        $expected->dob = $mockUserProfileData['dob'];
        $expected->gender = $mockUserProfileData['gender'];
        $expected->id = $mockUserProfile->id;

        $userProfileRepoMock = $this->mock(UserProfileRepositoryInterface::class, 
            function($mock) use ($mockUser, $mockUserProfile, $mockUserProfileData, $expected) {
                $mock->shouldReceive('update')
                ->once()
                ->with($mockUserProfile->id, $mockUserProfileData)
                ->andReturn($expected);
            });

        $service = new UserProfileService($userRepoMock, $userProfileRepoMock);

        $result = $service->update($mockUserProfile->id, $mockUserProfileData);

        $this->assertNotNull($result);
        $this->assertInstanceOf(UserProfile::class, $result);
        $this->assertInstanceOf(User::class, $result->user);

        $this->assertEquals($result->bio, $mockUserProfileData['bio']);
        $this->assertEquals($result->dob, $mockUserProfileData['dob']);
        $this->assertEquals($result->gender, $mockUserProfileData['gender']);

        $this->assertEquals($result->user->id, $mockUser->id);
        $this->assertEquals($result->user->email, $mockUser->email);
        $this->assertEquals($result->user->name, $mockUser->name);
    }

    private function createMockUser()
    {
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->name = fake()->name();
        $mockUser->email = fake()->email();
        $mockUser->id = 1;

        return $mockUser;
    }

    private function createMockUserProfile($mockUser)
    {
        $mockUserProfile = Mockery::mock(UserProfile::class)->makePartial();
        $mockUserProfile->user = $mockUser;
        $mockUserProfile->bio = fake()->sentence();
        $mockUserProfile->dob = fake()->date();
        $mockUserProfile->gender = 'male';
        $mockUserProfile->id = rand(1, 100);

        return $mockUserProfile;
    }
}
