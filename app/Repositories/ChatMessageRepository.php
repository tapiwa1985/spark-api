<?php 

namespace App\Repositories;

use App\Models\ChatMessage;
use App\Repositories\Contracts\ChatMessageRepositoryInterface;

class ChatMessageRepository extends BaseRepository implements ChatMessageRepositoryInterface 
{
    public function __construct(ChatMessage $model)
    {
        parent::__construct($model);
    }
}