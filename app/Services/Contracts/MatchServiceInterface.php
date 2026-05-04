<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface MatchServiceInterface extends BaseServiceInterface
{
    public function fetchMatchesForUser(int $userId): Collection;
}
