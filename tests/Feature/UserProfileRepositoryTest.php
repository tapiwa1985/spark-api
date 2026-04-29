<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Models\User;
use App\Models\UserProfile;

/**
 * Feature tests for user profile repository behavior.
 */
class UserProfileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserProfileRepositoryInterface $_userProfileRepository;

    /**
     * It resolves repository dependencies before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Resolve repository implementation from container bindings.
        $this->_userProfileRepository = app()->make(UserProfileRepositoryInterface::class);
    }

    /**
     * It creates a profile linked to an existing user.
     */
    public function testCreateUserProfile()
    {
        // Create a parent user because profile has a user_id foreign key.
        $user = User::factory()->create();

        // Create a new user profile using the repository
        $userProfileData = [
            'user_id' => $user->id,
            'bio' => fake()->paragraph,
            'dob' => fake()->date(),
            'gender' => 'male'
        ];

        $userProfile = $this->_userProfileRepository->create($userProfileData);

        // Assert profile fields and relationship values are correctly mapped.
        $this->assertNotNull($userProfile);
        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertInstanceOf(User::class, $userProfile->user);
        $this->assertEquals($userProfileData['user_id'], $userProfile->user_id);
        $this->assertEquals($userProfileData['bio'], $userProfile->bio);
        $this->assertEquals($userProfileData['dob'], $userProfile->dob);
        $this->assertEquals($userProfileData['gender'], $userProfile->gender);
        $this->assertEquals($userProfile->user->id, $user->id);
        $this->assertEquals($userProfile->user->name, $user->name);
        $this->assertEquals($userProfile->user->email, $user->email);

        // Assert the record exists in persistent storage.
        $this->assertDatabaseHas('user_profiles', [
            'id' => $userProfile->id,
            'user_id' => $userProfileData['user_id'],
            'bio' => $userProfileData['bio'],
            'dob' => $userProfileData['dob'],
            'gender' => $userProfileData['gender'],
        ]);
    }

    public function testGetUserProfileByEmail()
    {
        $userProfile = UserProfile::factory()->create();

        $result = $this->_userProfileRepository->findByEmail($userProfile->user->email);

        $this->assertNotNull($result);
        $this->assertInstanceOf(UserProfile::class, $result);
        $this->assertInstanceOf(User::class, $result->user);
        $this->assertEquals($result->bio, $userProfile->bio);
        $this->assertEquals($result->dob, $result->dob);
        $this->assertEquals($result->gender, $result->gender);
        $this->assertEquals($result->user->id, $userProfile->user->id);
        $this->assertEquals($userProfile->user->name, $userProfile->user->name);
        $this->assertEquals($userProfile->user->email, $userProfile->user->email);
    }
}
