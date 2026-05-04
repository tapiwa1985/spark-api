<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\CreateChatMessageRequest;
use App\Services\Contracts\ChatMessageServiceInterface;
use App\Events\MessageSent;

/**
 * Authenticated API for posting messages on a {@see \App\Models\UserMatch} thread.
 * The sender is always the signed-in user; body and match context are validated by {@see CreateChatMessageRequest}.
 *
 * @package App\Http\Controllers\Api
 */
class ChatMessageController extends Controller
{
    /**
     * Persists chat rows and returns them as {@see ChatMessageResource} JSON.
     */
    private ChatMessageServiceInterface $_chatMessageService;

    /**
     * @param ChatMessageServiceInterface $chatMessageService Domain service for creating {@see \App\Models\ChatMessage} records.
     */
    public function __construct(ChatMessageServiceInterface $chatMessageService)
    {
        $this->_chatMessageService = $chatMessageService;
    }

    /**
     * Creates a message for the given `user_match_id`, stamps `sender_id` from the JWT user, and responds with `201 Created`.
     *
     * @param CreateChatMessageRequest $request
     * @return JsonResponse Single {@see ChatMessageResource} payload (wrapped per Laravel’s resource response conventions).
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
}
