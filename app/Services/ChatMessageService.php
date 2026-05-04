<?php 

namespace App\Services;

use App\Repositories\Contracts\ChatMessageRepositoryInterface;
use App\Services\Contracts\ChatMessageServiceInterface;

class ChatMessageService extends BaseService implements ChatMessageServiceInterface 
{
    protected ChatMessageRepositoryInterface $chatMessageRepository;

    public function __construct(ChatMessageRepositoryInterface $chatMessageRepository)
    {
        parent::__construct($chatMessageRepository);

        $this->chatMessageRepository = $chatMessageRepository;
    }
}