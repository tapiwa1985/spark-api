<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\ProfileImage;
use App\Repositories\Contracts\ProfileImageRepositoryInterface;
use App\Services\ProfileImageService;
use Mockery as m;

class ProfileImageServiceUnitTest extends TestCase
{
    public function testCreateProfileImage()
    {
        $profileImageMock = m::mock(ProfileImage::class)->makePartial();
        $profileImageMock->image_url = fake()->url();
        $profileImageMock->display_order = 1;
        $profileImageMock->is_display = true;
        $profileImageMock->user_profile_id = 1;

        $userProfileMock = m::mock(UserProfile::class)->makePartial();
        $userProfileMock->id = 1;

         $imageData = [
            'user_profile_id' => 1,
            'image_url' => $profileImageMock->image_url,
            'display_order' => 1,
            'is_display' => true,
        ];

        $profileImageRepoMock = $this->mock(ProfileImageRepositoryInterface::class,
            function($mock) use($imageData, $profileImageMock) {
                $mock->shouldReceive('create')
                ->once()
                ->with([
                    'user_profile_id' => 1,
                    'image_url' => $profileImageMock->image_url,
                    'display_order' => 1,
                    'is_display' => true,
                ])->andReturn($profileImageMock);
            });

            $service = new ProfileImageService($profileImageRepoMock);

            $result = $service->create($imageData);

            $this->assertNotNull($result);
            $this->assertInstanceOf(ProfileImage::class, $result);
            $this->assertEquals($result->image_url, $imageData['image_url']);
            $this->assertEquals($result->user_profile_id, $imageData['user_profile_id']);
            $this->assertEquals($result->display_order, $imageData['display_order']);
            $this->assertTrue($result->is_display);
    }
}
