<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface ChatMessageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Fetch list of chat messages for a match
     *
     * @param int $userMatchId
     * @return Collection
     */
    public function getMessagesForMatch(int $userMatchId): Collection;
}
