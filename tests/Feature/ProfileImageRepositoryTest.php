<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserProfile;
use App\Models\ProfileImage;
use App\Repositories\Contracts\ProfileImageRepositoryInterface;

class ProfileImageRepositoryTest extends TestCase
{
    private ProfileImageRepositoryInterface $_profileImageRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_profileImageRepository = app()->make(ProfileImageRepositoryInterface::class);
    }

    public function testCreateProfileImage()
    {
        $userProfile = UserProfile::factory()->create();

        $profileImageData = [
            'image_url' => fake()->url(),
            'display_order' => rand(0,5),
            'is_display' => true,
            'user_profile_id' => $userProfile->id,
            'caption' => fake()->sentence,
        ];

        $result = $this->_profileImageRepository->create($profileImageData);

        $this->assertDatabaseHas('profile_images', $profileImageData);
        $this->assertInstanceOf(ProfileImage::class, $result);
        $this->assertTrue($result->is_display);
        $this->assertEquals($result->image_url, $profileImageData['image_url']);
        $this->assertEquals($result->display_order, $profileImageData['display_order']);
        $this->assertEquals($result->user_profile_id, $profileImageData['user_profile_id']);
        $this->assertEquals($result->caption, $profileImageData['caption']);
    }
}
