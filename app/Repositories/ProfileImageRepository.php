<?php

namespace App\Repositories;

use App\Models\ProfileImage;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ProfileImageRepositoryInterface;

class ProfileImageRepository extends BaseRepository implements ProfileImageRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param ProfileImage
     */
    public function __construct(ProfileImage $model)
    {
        parent::__construct($model);
    }
}
