<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Storage::fake('gcs');
        $imageToUpload = UploadedFile::fake()->image('test.jpg', 800, 600);

        $result = $this->_imageUploader->uploadImage($imageToUpload);

        $this->assertIsString($result);
        $uploaded = Storage::disk('gcs')->allFiles();
        $this->assertNotEmpty($uploaded);
        $this->assertMatchesRegularExpression('#^profiles/.+\.jpg$#', $uploaded[0]);
    }
}
