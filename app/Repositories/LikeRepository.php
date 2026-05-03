<?php

namespace App\Repositories;

use App\Models\Like;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\LikeRepositoryInterface;

class LikeRepository extends BaseRepository implements LikeRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param Like $model
     */
    public function __construct(Like $model)
    {
        parent::__construct($model);
    }
}
