<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\ProfileImageService;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Utils\Contracts\ImageUploaderInterface;

class ImageUploaderTest extends TestCase
{
    private ImageUploaderInterface $_imageUploader;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_imageUploader = app()->make(ImageUploaderInterface::class);
    }
    public function testUploadProfilePicture()
    {
        $userProfile = UserProfile::factory()->create();
        Storage::fake('gcs');
        $imageToUpload = UploadedFile::fake()->image('test.jpg', 800, 600);

        $result = $this->_imageUploader->uploadImage($imageToUpload);

        Storage::disk('gcs')->assertExists($result);
    }
}
