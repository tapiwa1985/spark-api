<?php

namespace App\Utils\Contracts;

interface ImageUploaderInterface
{
    /**
     * @param \Illuminate\Http\UploadedFile|\SplFileInfo $file Readable upload or temp path consumed by the implementation.
     * @return string|null                               Public URL of the stored object, or null when upload cannot complete.
     */
    public function uploadImage($file): ?string;
}
