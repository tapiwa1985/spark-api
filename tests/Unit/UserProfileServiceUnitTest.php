<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\UserProfileService;

class UserProfileServiceUnitTest extends TestCase
{
    public function testCreateUserProfile()
    {
        $mockUserData = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ];

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->id = 1;
        $mockUser->name = $mockUserData['name'];
        $mockUser->email = $mockUserData['email'];
        $mockUser->password = $mockUserData['password'];

        $mockUserProfileData = [
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        $mockUserProfile = Mockery::mock(UserProfile::class)->makePartial();
        $mockUserProfile->user = $mockUser;
        $mockUserProfile->bio = $mockUserProfileData['bio'];
        $mockUserProfile->dob = $mockUserProfileData['dob'];
        $mockUserProfile->gender = $mockUserProfileData['gender'];

        $userRepoMock = $this->mock(UserRepositoryInterface::class,
            function($mock) use($mockUserData, $mockUser) {
                $mock->shouldReceive('create')
                ->once()
                ->with($mockUserData)
                ->andReturn($mockUser);
            });
        
        $userProfileRepoMock = $this->mock(UserProfileRepositoryInterface::class,
            function($mock) use($mockUserProfileData, $mockUserProfile, $mockUser) {
                $mock->shouldReceive('create')
                ->once()
                ->with([
                    'user_id' => $mockUser->id,
                    'bio' => $mockUserProfileData['bio'],
                    'dob' => $mockUserProfileData['dob'],
                    'gender' => $mockUserProfileData['gender'],
                ])
                ->andReturn($mockUserProfile);
            });
        
        $service = new UserProfileService($userRepoMock, $userProfileRepoMock);

        $result = $service->create(array_merge($mockUserData, $mockUserProfileData));

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
