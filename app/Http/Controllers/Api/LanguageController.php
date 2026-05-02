<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LanguageResourceCollection;
use App\Services\Contracts\LanguageServiceInterface;

/**
 * Read-only listing of spoken languages for profile or filter metadata.
 *
 * @package App\Http\Controllers\Api
 */
class LanguageController extends Controller
{
    /**
     * Domain service used to load {@see \App\Models\Language} records.
     */
    private LanguageServiceInterface $_languageService;

    /**
     * @param LanguageServiceInterface $languageService Bound implementation for taxonomy reads.
     */
    public function __construct(LanguageServiceInterface $languageService)
    {
        $this->_languageService = $languageService;
    }

    /**
     * @return LanguageResourceCollection All languages as API resources.
     */
    public function index(): LanguageResourceCollection
    {
        $languages = $this->_languageService->all();

        return new LanguageResourceCollection($languages);
    }
}
