<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return Model|null
     */
    public function findByEmail(string $email): ?Model;

    /**
     * Find a user by their LinkedIn ID.
     *
     * @param string $linkedinId
     * @return Model|null
     */
    public function findByLinkedInId(string $linkedinId): ?Model;
}
