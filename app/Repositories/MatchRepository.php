<?php

namespace App\Repositories;

use App\Models\UserMatch;
use App\Repositories\Contracts\MatchRepositoryInterface;

class MatchRepository extends BaseRepository implements MatchRepositoryInterface
{
    public function __construct(UserMatch $model)
    {
        parent::__construct($model);
    }
}
