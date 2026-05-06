<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Service contract for managing chat message operations.
 *
 * This interface defines the contract for all chat message-related business logic,
 * including message retrieval, status updates, and other messaging operations.
 * Implementations should handle authorization, validation, and persistence logic
 * for chat messages within match conversations.
 *
 * @package App\Services\Contracts
 */
interface ChatMessageServiceInterface extends BaseServiceInterface
{
    /**
     * Retrieve all chat messages for a specific match conversation.
     *
     * This method fetches messages belonging to a particular user match thread,
     * typically ordered chronologically for conversation display. The returned
     * collection may include pagination metadata depending on the implementation.
     *
     * @param int $userMatchId The unique identifier of the user match (conversation thread)
     * @return Collection Collection of chat message models (typically with pagination)
     */
    public function getMessagesForMatch(int $userMatchId): Collection;

    /**
     * Update the read status of a specific chat message.
     *
     * This method marks a message as read or unread by the recipient, typically
     * used for read receipt functionality. The update data should specify which
     * recipient is marking the message read and the new status.
     *
     * @param int $chatMessageId The unique identifier of the chat message
     * @param array $data The data containing read status information
     */
    public function updateReadStatus(int $chatMessageId, array $data): ?Model;
}
