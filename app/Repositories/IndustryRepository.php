<?php

namespace App\Repositories;

use App\Models\Industry;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\IndustryRepositoryInterface;

/**
 * Simple CRUD over {@see Industry} taxonomy rows.
 */
class IndustryRepository extends BaseRepository implements IndustryRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param Industry $model Industry root model.
     */
    public function __construct(Industry $model)
    {
        parent::__construct($model);
    }
}
