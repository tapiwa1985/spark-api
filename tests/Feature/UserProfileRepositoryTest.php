<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Industry;
use App\Models\Interest;

/**
 * Feature tests for user profile repository behavior.
 */
class UserProfileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var UserProfileRepositoryInterface
     */
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

        $industry = Industry::factory()->create();

        // Create a new user profile using the repository
        $userProfileData = [
            'user_id' => $user->id,
            'bio' => fake()->paragraph,
            'dob' => fake()->date(),
            'gender' => 'male',
            'industry_id' => $industry->id,
            'job_title' => fake()->jobTitle(),
        ];

        $userProfile = $this->_userProfileRepository->create($userProfileData);

        // Assert profile fields and relationship values are correctly mapped.
        $this->assertNotNull($userProfile);
        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertInstanceOf(User::class, $userProfile->user);
        $this->assertInstanceOf(Industry::class, $userProfile->industry);

        $this->assertEquals($userProfileData['user_id'], $userProfile->user_id);
        $this->assertEquals($userProfileData['bio'], $userProfile->bio);
        $this->assertEquals($userProfileData['dob'], $userProfile->dob);
        $this->assertEquals($userProfileData['gender'], $userProfile->gender);
        $this->assertEquals($userProfileData['job_title'], $userProfile->job_title);
        $this->assertEquals($userProfileData['industry_id'], $userProfile->industry_id);

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
            'industry_id' => $industry->id,
            'job_title' => $userProfileData['job_title'],
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

    /**
     * Tests update user profile details
     * 
     * @return void
     */
    public function testUpdateUserProfile()
    {
        $userProfile = UserProfile::factory()->create();

        $userProfileData = [
            'bio' => fake()->paragraph,
            'dob' => fake()->date(),
            'gender' => 'male'
        ];

        $result = $this->_userProfileRepository->update($userProfile->id, $userProfileData);

        $this->assertNotNull($userProfile);

        $this->assertInstanceOf(UserProfile::class, $userProfile);
        $this->assertInstanceOf(User::class, $userProfile->user);

        $this->assertEquals($userProfileData['bio'], $result->bio);
        $this->assertEquals($userProfileData['dob'], $result->dob);
        $this->assertEquals($userProfileData['gender'], $result->gender);
        $this->assertEquals($userProfile->user->id, $result->user->id);
        $this->assertEquals($userProfile->user->name, $result->user->name);
        $this->assertEquals($userProfile->user->email, $result->user->email);

        // Assert the record exists in persistent storage.
        $this->assertDatabaseHas('user_profiles', [
            'id' => $userProfile->id,
            'user_id' => $userProfile->user->id,
            'bio' => $userProfileData['bio'],
            'dob' => $userProfileData['dob'],
            'gender' => $userProfileData['gender'],
        ]);
    }

    public function testAddInterestsToUserProfile()
    {
        $userProfile = UserProfile::factory()->create();
        $interests = Interest::factory(3)->create();

        $userProfile->interests()->attach($interests->pluck('id')->toArray());

        $this->assertCount(3, $userProfile->interests);
        foreach ($interests as $interest) {
            $this->assertTrue($userProfile->interests->contains($interest));
        }
    }
}
