<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use App\Models\BlockedUser;
use App\Repositories\Contracts\BlockedUserRepositoryInterface;

/**
 * Class BlockedUserRepository
 *
 * Handles database operations for blocked user records.
 * Implements the BlockedUserRepositoryInterface to provide a concrete
 * implementation for blocking/unblocking logic, typically used by services
 * that manage user safety and content moderation.
 *
 * This repository extends the base repository to inherit generic CRUD methods
 * and injects the specific BlockedUser model for Eloquent operations.
 *
 * @package App\Repositories
 */
class BlockedUserRepository extends BaseRepository implements BlockedUserRepositoryInterface
{
    /**
     * The Eloquent model instance being managed by this repository.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * BlockedUserRepository constructor.
     *
     * Injects the BlockedUser model and passes it to the parent BaseRepository
     * to enable core database operations (find, create, update, delete, etc.)
     * specifically for the blocked_users table.
     *
     * @param BlockedUser $model The BlockedUser model instance.
     */
    public function __construct(BlockedUser $model)
    {
        parent::__construct($model);
    }
}
