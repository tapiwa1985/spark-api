<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserMatch;
use App\Models\ChatMessage;

/**
 * Authorization policy for chat message operations.
 *
 * This policy defines the authorization rules for chat message actions
 * such as updating, deleting, or viewing messages within match conversations.
 *
 * @package App\Policies
 */
class ChatMessagePolicy
{
    /**
     * Determine if a user can update a specific chat message.
     *
     * This method checks whether the authenticated user is one of the
     * participants in the match conversation where the message belongs.
     * Users can only update messages (e.g., mark as read, edit content)
     * if they are either the original sender or the recipient.
     *
     * @param User $user The authenticated user attempting the update
     * @param ChatMessage $chatMessage The chat message being updated
     * @return bool True if the user is a participant in the match, false otherwise
     */
    public function update(User $user, ChatMessage $chatMessage): bool
    {
        $userIds = $this->getUserIds($user, $chatMessage->userMatch);
        return in_array($user->id, $userIds);
    }

    /**
     * Determine if a user can view messages in a specific match conversation.
     *
     * This method checks whether the authenticated user is a participant
     * in the match, granting them permission to view the conversation thread.
     * Only the two matched users can access messages exchanged between them.
     *
     * @param User $user The authenticated user attempting to view messages
     * @param UserMatch $userMatch The match conversation being accessed
     * @return bool True if the user is a participant in the match, false otherwise
     */
    public function getMessages(User $user, UserMatch $userMatch): bool
    {
        $userIds = $this->getUserIds($user, $userMatch);

        return in_array($user->id, $userIds);
    }

    /**
     * Get the IDs of both participants in a match conversation.
     *
     * This helper method extracts the user IDs from a UserMatch relationship,
     * returning an array containing both the initiating user and the matched user.
     * These IDs represent the only two users authorized to interact with messages
     * in this conversation thread.
     *
     * @param User $user The authenticated user (used for context, not directly used in current implementation)
     * @param UserMatch $userMatch The match containing participant IDs
     * @return array An array containing both user IDs from the match
     */
    private function getUserIds(User $user, UserMatch $userMatch): array
    {
        $users = [];
        array_push($users, $userMatch->user_id);
        array_push($users, $userMatch->matched_user_id);

        return $users;
    }
}
