<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UserServiceInterface extends BaseServiceInterface
{
    /**
     * Get a user by their email address.
     *
     * @param string $email
     * @return Model|null
     */
    public function getUserByEmail(string $email): ?Model;
}
