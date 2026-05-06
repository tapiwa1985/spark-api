<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatMessageResourceCollection;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\CreateChatMessageRequest;
use App\Services\Contracts\ChatMessageServiceInterface;
use App\Services\Contracts\MatchServiceInterface;
use App\Events\MessageRead;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\GetChatMessagesRequest;

/**
 * Authenticated API for posting messages on a {@see \App\Models\UserMatch} thread.
 * The sender is always the signed-in user; body and match context are validated by {@see CreateChatMessageRequest}.
 *
 * @package App\Http\Controllers\Api
 */
class ChatMessageController extends Controller
{
    /**
     * Domain service for chat message persistence and retrieval.
     */
    private ChatMessageServiceInterface $_chatMessageService;

    /**
     * Domain service for match validation and lookup.
     */
    private MatchServiceInterface $_matchService;

    /**
     * Inject required domain services.
     *
     * @param ChatMessageServiceInterface $chatMessageService
     * @param MatchServiceInterface $matchService
     */
    public function __construct(ChatMessageServiceInterface $chatMessageService, MatchServiceInterface $matchService)
    {
        $this->_chatMessageService = $chatMessageService;
        $this->_matchService = $matchService;
    }

    /**
     * Retrieve paginated chat messages for a specific match (conversation).
     *
     * This method uses a `GetChatMessagesRequest` to validate the `match_id` query parameter.
     * It then authorizes the authenticated user to view messages for that match via a `Gate` policy.
     * Finally, it fetches messages from the service and returns them as a resource collection.
     *
     * @param GetChatMessagesRequest $request The validated request containing the match ID.
     * @return ChatMessageResourceCollection A collection of chat message resources (wrapped with pagination metadata).
     */
    public function index(GetChatMessagesRequest $request): ChatMessageResourceCollection
    {
        $matchId = $request->input('match_id');
        $userMatch = $this->_matchService->find((int)$matchId);
        Gate::authorize('getMessages', $userMatch);

        $chatMessages = $this->_chatMessageService->getMessagesForMatch($matchId);

        return new ChatMessageResourceCollection($chatMessages);
    }

    /**
     * Create a new chat message and broadcast it to the match participants.
     *
     * The method extracts `message` and `user_match_id` from the request, sets `sender_id` to the currently
     * authenticated user, and persists the message using the chat message service.
     * After creation, it broadcasts a `MessageSent` event to all other participants (excluding the sender)
     * via Laravel Reverb/Pusher.
     *
     * The response is a `201 Created` with a single `ChatMessageResource` representation.
     *
     * @param CreateChatMessageRequest $request The validated request containing the message and match ID.
     * @return JsonResponse JSON response containing the created message resource.
     */
    public function store(CreateChatMessageRequest $request): JsonResponse
    {
        $data = $request->only('message', 'user_match_id');
        $userId = auth()->user()->id;
        $data['sender_id'] = $userId;

        $chatMessage = $this->_chatMessageService->create($data);

        broadcast(new MessageSent($chatMessage))->toOthers();

        return (new ChatMessageResource($chatMessage))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Mark a message as read and broadcast to the other participant on the match channel.
     */
    public function update(string $chatMessageId): ChatMessageResource
    {
        $chatMessage = $this->_chatMessageService->find((int) $chatMessageId);
        Gate::authorize('update', $chatMessage);

        $updatedChatMessage = $this->_chatMessageService->updateReadStatus((int) $chatMessageId, []);

        broadcast(new MessageRead($updatedChatMessage))->toOthers();

        return new ChatMessageResource($updatedChatMessage);
    }
}
