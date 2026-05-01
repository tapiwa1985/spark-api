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
     * @param \Illuminate\Http\UploadedFile|\SplFileInfo $file
     * @return string Permanent public URL for the object (same shape as Storage::disk('gcs')->url()).
     */
    public function uploadImage($file): ?string
    {
        $image = Image::decode($file)
            ->cover(500, 500);

        $extension = $file->extension();

        $filename = 'profiles/' . Str::uuid() . '.' . $extension;

        $tempFilePath = storage_path("temp/{$filename}");
        File::ensureDirectoryExists(dirname($tempFilePath));

        try {
            $image->save($tempFilePath);

            $contents = file_get_contents($tempFilePath);
            if ($contents === false) {
                throw new \RuntimeException('Could not read the processed image from disk.');
            }

            $disk = Storage::disk('gcs');
            $disk->put($filename, $contents);

            // Persist a full https URL so clients can use it in <img src> (not just the object path).
            return $disk->url($filename);
        } finally {
            if (File::exists($tempFilePath)) {
                File::delete($tempFilePath);
            }
        }
    }
}
