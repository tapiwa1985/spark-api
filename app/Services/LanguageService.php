<?php

namespace App\Services;

use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Services\Contracts\LanguageServiceInterface;

/**
 * Taxonomy read access for {@see \App\Models\Language}.
 */
class LanguageService extends BaseService implements LanguageServiceInterface
{
    /**
     * @param LanguageRepositoryInterface $repository Language listing repository.
     */
    public function __construct(LanguageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
