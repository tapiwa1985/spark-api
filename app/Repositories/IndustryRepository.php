<?php

namespace App\Repositories;

use App\Models\Industry;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\IndustryRepositoryInterface;

class IndustryRepository extends BaseRepository implements IndustryRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * IndustryRepository constructor.
     *
     * @param Industry
     */
    public function __construct(Industry $model)
    {
        parent::__construct($model);
    }
}
