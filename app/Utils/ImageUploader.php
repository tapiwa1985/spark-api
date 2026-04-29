<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Utils\Contracts\ImageUploaderInterface;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploader implements ImageUploaderInterface
{
    /**
     * @param File
     * @return string|null
     */
    public function uploadImage($file): ?string
    {
        $image = Image::decode($file)
            ->cover(500, 500);

        $extension = $file->extension();

        $filename = 'profiles/' . Str::uuid() . '.' . $extension;

        $tempFilePath = storage_path("temp/{$filename}");
        File::ensureDirectoryExists(dirname($tempFilePath));

        $image->save($tempFilePath);

        $uploaded = Storage::disk('gcs')->put($filename, file_get_contents($tempFilePath));

        if ($uploaded) {
            return $filename;
        }

        return null;
    }
}
