<?php

namespace App\Services\Contracts;

/**
 * Interface BlockedUserServiceInterface
 *
 * Defines the contract for the service that handles business logic related to
 * blocking/unblocking users. This service acts as an intermediary between
 * controllers/repositories and encapsulates rules such as:
 * - Preventing a user from blocking themselves
 * - Checking block limits or rate limiting
 * - Broadcasting block events (e.g., deleting chats, hiding matches)
 * - Coordinating with other services (e.g., notification, match, report)
 *
 * Extends the base service interface to inherit common service methods
 * (e.g., get, create, update, delete) where applicable.
 *
 * @package App\Services\Contracts
 */
interface BlockedUserServiceInterface extends BaseServiceInterface
{
}
