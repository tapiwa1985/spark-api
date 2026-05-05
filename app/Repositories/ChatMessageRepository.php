<?php

namespace App\Repositories;

use App\Models\ChatMessage;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;

class ChatMessageRepository extends BaseRepository implements ChatMessageRepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param ChatMessage
     */
    public function __construct(ChatMessage $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

     /**
     * Fetch list of chat messages for a match
     *
     * @param int $userMatchId
     * @return Collection
     */
    public function getMessagesForMatch(int $userMatchId): Collection
    {
        return $this->model
            ->where('user_match_id', $userMatchId)
            ->get();
    }
}
