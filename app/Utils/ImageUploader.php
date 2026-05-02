<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Utils\Contracts\ImageUploaderInterface;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Resizes an uploaded file to a fixed cover size, writes it to the `gcs` disk under `profiles/`, and returns the
 * public URL suitable for image tags. Temporary files under `storage/temp/` are removed in a `finally` block.
 */
class ImageUploader implements ImageUploaderInterface
{
    /**
     * @param \Illuminate\Http\UploadedFile|\SplFileInfo $file Raw upload from the request.
     * @return string|null                                 Public object URL from {@see \Illuminate\Filesystem\FilesystemAdapter::url}, or would have been null if put failed (disk is configured to throw on error).
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

            return $disk->url($filename);
        } finally {
            if (File::exists($tempFilePath)) {
                File::delete($tempFilePath);
            }
        }
    }
}
