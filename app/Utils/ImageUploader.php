<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class ImageUploader implements ImageUploaderInterface
{
    /**
     * @param File
     * @return string|null
     */
    public function uploadImage(File $file): ?string
    {
        $manager = new ImageManager(new Driver());

        $image = $manager->read($file->getPathname());

        $image->scale(width:800);

        $encodedImage = $image->toJpeg(85);

        $filename = 'images/' . Str::uuid() . '.jpg';

        $uploaded = Storage::disk('gcs')->put($filename, (string) $encodedImage);

        if ($uploaded) {
            return Storage::disk('gcs')->url($filename);
        }

        return null;
    }
}
