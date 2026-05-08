<?php

namespace App\Repositories;

use App\Models\UserRejection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserRejectionRepositoryInterface;

/**
 * Repository for handling user rejection data operations.
 *
 * Implements the contract defined by UserRejectionRepositoryInterface
 * and extends the base repository to provide CRUD functionality for UserRejection models.
 *
 * @package App\Repositories
 */
class UserRejectionRepository extends BaseRepository implements UserRejectionRepositoryInterface
{
    /**
     * The model instance associated with the repository.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Create a new UserRejectionRepository instance.
     *
     * @param UserRejection $model The UserRejection model instance.
     */
    public function __construct(UserRejection $model)
    {
        parent::__construct($model);
    }
}
