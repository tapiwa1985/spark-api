<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var Model $model
     */
    protected Model $model;

    /**
     * UserRepository constrictor
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return Model|null
     */
    public function findByEmail(string $email): ?Model
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Find a user by their LinkedIn ID.
     *
     * @param string $linkedinId
     * @return Model|null
     */
    public function findByLinkedInId(string $linkedinId): ?Model
    {
        return $this->model->with('userProfile')->where('linkedin_id', $linkedinId)->first();
    }
}
