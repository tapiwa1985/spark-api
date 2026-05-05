<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface ChatMessageServiceInterface extends BaseServiceInterface
{
    public function getMessagesForMatch(int $userMatchId): Collection;
}
