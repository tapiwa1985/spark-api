<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    private Model $model;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
