<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * {@see User} persistence with specialized finders for email and LinkedIn identity.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var Model $model
     */
    protected Model $model;

    /**
     * @param User $model Auth user root model.
     */
    public function __construct(User $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    /**
     * @return Model|null First user row matching email, if any.
     */
    public function findByEmail(string $email): ?Model
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @return Model|null User with eager-loaded `userProfile` when LinkedIn id matches.
     */
    public function findByLinkedInId(string $linkedinId): ?Model
    {
        return $this->model->with('userProfile')->where('linkedin_id', $linkedinId)->first();
    }
}
