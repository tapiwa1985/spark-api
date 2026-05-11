<?php

namespace App\Repositories\Contracts;

/**
 * Interface BlockedUserRepositoryInterface
 *
 * Defines the contract for a repository that manages blocked user records.
 * Extends the base repository interface to inherit common CRUD and query methods.
 *
 * Implementations of this interface should handle data persistence logic for
 * blocking/unblocking users, checking block statuses, and retrieving lists of
 * blocked users. This abstraction allows the service layer to remain agnostic
 * of the underlying data source (e.g., Eloquent, MongoDB, external API).
 *
 * @package App\Repositories\Contracts
 */
interface BlockedUserRepositoryInterface extends BaseRepositoryInterface
{
}
