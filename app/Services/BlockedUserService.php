<?php

namespace App\Services;

use App\Repositories\Contracts\BlockedUserRepositoryInterface;
use App\Services\Contracts\BlockedUserServiceInterface;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\UserMatch;

/**
 * Class BlockedUserService
 *
 * Implements the business logic for managing blocked user relationships.
 * This service acts as an intermediary between controllers and the blocked user repository,
 * enforcing application rules such as preventing self-blocks, validating input,
 * and coordinating with other services (e.g., match service, chat service) when a block occurs.
 *
 * Extends the base service to inherit common service methods and relies on the
 * blocked user repository for data persistence.
 *
 * @package App\Services
 */
class BlockedUserService extends BaseService implements BlockedUserServiceInterface
{
    /**
     * The repository instance responsible for blocked user data operations.
     *
     * @var BlockedUserRepositoryInterface
     */
    protected BlockedUserRepositoryInterface $blockedUserRepository;

    /**
     * The repository instance responsible for match data operations.
     * Used to retrieve and update matches that involve the blocked user.
     *
     * @var MatchRepositoryInterface
     */
    private MatchRepositoryInterface $_matchRepository;

    /**
     * BlockedUserService constructor.
     *
     * Injects the blocked user repository and match repository.
     * Passes the blocked user repository to the parent BaseService for generic CRUD operations.
     *
     * @param BlockedUserRepositoryInterface $blockedUserRepository Repository for blocked user persistence.
     * @param MatchRepositoryInterface $matchRepository Repository for match persistence.
     */
    public function __construct(BlockedUserRepositoryInterface $blockedUserRepository, MatchRepositoryInterface $matchRepository)
    {
        parent::__construct($blockedUserRepository);

        $this->_matchRepository = $matchRepository;
    }

    /**
     * Create a new blocked user record and update any affected matches.
     *
     * This method overrides the base `create` method to add transactional logic.
     * After creating the block, it fetches all matches for the blocker and updates
     * any match that involves the blocked user (on either side) to the BLOCKED status.
     * The entire operation is wrapped in a database transaction to ensure data consistency.
     *
     * @param array $data The data for creating the blocked user record.
     * @return Model The newly created blocked user model instance.
     */
    public function create(array $data): Model
    {
        $matches = $this->_matchRepository->getMatchesForUser($data['user_id']);

        return DB::transaction(function () use ($data, $matches) {
            $blockedUser = parent::create($data);

            foreach ($matches as $match) {
                if (
                    (int) $match->user_id === (int) $data['blocked_user_id']
                    || (int) $match->matched_user_id === (int) $data['blocked_user_id']
                ) {
                    $this->_matchRepository->update((int) $match->user_match_id, ['status' => UserMatch::USER_MATCH_STATUS_BLOCKED]);
                }
            }

            return $blockedUser;
        });
    }
}
