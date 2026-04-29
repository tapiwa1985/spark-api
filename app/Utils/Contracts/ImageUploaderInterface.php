<?php

namespace App\Utils\Contracts;

use Illuminate\Support\Facades\File;

interface ImageUploaderInterface
{
    /**
     * @param File
     * @return string|null
     */
    public function uploadImage(File $file): ?string;
}
