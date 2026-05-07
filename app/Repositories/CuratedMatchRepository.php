<?php

namespace App\Repositories;

use App\Models\CuratedMatch;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\CuratedMatchRepositoryInterface;

/**
 * Class CuratedMatchRepository
 *
 * Repository class for handling database operations related to curated matches.
 * Extends the base repository and implements the curated match repository interface.
 *
 * @package App\Repositories
 */
class CuratedMatchRepository extends BaseRepository implements CuratedMatchRepositoryInterface
{
    /**
     * @var Model The model instance for curated match
     */
    protected Model $model;

    /**
     * CuratedMatchRepository constructor.
     *
     * @param CuratedMatch $model The curated match model instance
     */
    public function __construct(CuratedMatch $model)
    {
        parent::__construct($model);
    }
}
