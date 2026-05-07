<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\CuratedMatchesWindow;
use App\Repositories\Contracts\CuratedMatchesWindowRepositoryInterface;

/**
 * Class CuratedMatchesWindowRepository
 *
 * Repository class for handling database operations related to curated matches windows.
 * Extends the base repository and implements the curated matches window repository interface.
 *
 * @package App\Repositories
 */
class CuratedMatchesWindowRepository extends BaseRepository implements CuratedMatchesWindowRepositoryInterface
{
    /**
     * @var Model The model instance for curated matches window
     */
    protected Model $model;

    /**
     * CuratedMatchesWindowRepository constructor.
     *
     * @param CuratedMatchesWindow $model The curated matches window model instance
     */
    public function __construct(CuratedMatchesWindow $model)
    {
        parent::__construct($model);
    }
}
