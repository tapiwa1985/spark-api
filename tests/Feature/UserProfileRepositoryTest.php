<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserProfileRepositoryInterface $userProfileRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userProfileRepository = app()->make(UserProfileRepositoryInterface::class);
    }

    /**
     * Test the UserProfileRepository's create method.
     *
     * @return void
     */
    public function testCreateUserProfile()
    {
        $user = User::factory()->create();

        // Create a new user profile using the repository
        $userProfileData = [
            'user_id' => $user,
            'bio' => fake()->paragraph,
            'dob' => fake()->date(),
            'gender' => 'male'
        ];

        $userProfile = $this->userProfileRepository->create($userProfileData);

        // Assert that the user profile was created successfully
        $this->assertNotNull($userProfile);
        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertEquals($userProfileData['user_id'], $userProfile->user_id);
        $this->assertEquals($userProfileData['bio'], $userProfile->bio);
        $this->assertEquals($userProfileData['dob'], $userProfile->dob);
        $this->assertEquals($userProfileData['gender'], $userProfile->gender);

        $this->assertDatabaseHas('user_profiles', [
            'id' => $userProfile->id,
            'user_id' => $userProfileData['user_id'],
            'bio' => $userProfileData['bio'],
            'dob' => $userProfileData['dob'],
            'gender' => $userProfileData['gender'],
        ]);
    }
}
