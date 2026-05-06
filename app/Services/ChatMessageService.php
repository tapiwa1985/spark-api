<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;
use App\Services\Contracts\ChatMessageServiceInterface;

/**
 * Service class for managing chat message business logic.
 *
 * This service handles all chat message operations including retrieval,
 * status updates, and other messaging functionality. It acts as an
 * intermediary between controllers and repositories, applying business
 * rules and validation before persisting or retrieving data.
 *
 * @package App\Services
 */
class ChatMessageService extends BaseService implements ChatMessageServiceInterface
{
    /**
     * The repository instance for chat message data operations.
     *
     * @var ChatMessageRepositoryInterface
     */
    protected ChatMessageRepositoryInterface $chatMessageRepository;

    /**
     * Create a new ChatMessageService instance.
     *
     * @param ChatMessageRepositoryInterface $chatMessageRepository Repository for chat message persistence
     */
    public function __construct(ChatMessageRepositoryInterface $chatMessageRepository)
    {
        parent::__construct($chatMessageRepository);
        $this->chatMessageRepository = $chatMessageRepository;
    }

    /**
     * Retrieve all chat messages for a specific match conversation.
     *
     * This method delegates directly to the repository layer to fetch messages
     * belonging to the specified user match thread. The collection is typically
     * ordered chronologically and may include eager-loaded relationships like
     * the message sender.
     *
     * @param int $userMatchId The unique identifier of the user match (conversation thread)
     * @return Collection Collection of chat message models for the specified match
     */
    public function getMessagesForMatch(int $userMatchId): Collection
    {
        return $this->chatMessageRepository->getMessagesForMatch($userMatchId);
    }

    /**
     * Update the read status of a specific chat message.
     *
     * This method marks a message as read by setting the read_at timestamp
     * to the current date and time. The implementation currently ignores the
     * provided $data array and always sets read_at to now(), which is suitable
     * for automatic read receipt functionality when a user views a message.
     *
     * Note: This implementation does not support marking messages as unread
     * or customizing the read timestamp. For more advanced read receipt
     * functionality (e.g., tracking which users have read a message in
     * group conversations), the method signature and implementation would
     * need to be enhanced.
     *
     * @param int $chatMessageId The unique identifier of the chat message to update
     * @param array $data The data containing read status information (currently ignored)
     * @return Model|null The updated chat message model, or null if update failed
     */
    public function updateReadStatus(int $chatMessageId, array $data): ?Model
    {
        return $this->chatMessageRepository->update($chatMessageId, ['read_at' => now()]);
    }
}
