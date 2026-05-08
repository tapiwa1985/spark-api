<?php

namespace App\Jobs;

use Carbon\CarbonImmutable;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Contracts\CuratedMatchServiceInterface;

class ProcessWeeklyCuratedMatchesChunkJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 1200;

    public function __construct(
        public readonly int $startUserId,
        public readonly int $endUserId,
        public readonly string $windowStartIso,
        public readonly int $maxItems,
        public readonly int $maxDistanceKm
    ) {
    }

    public function handle(CuratedMatchServiceInterface $curatedMatchService): void
    {
        $windowStart = CarbonImmutable::parse($this->windowStartIso);

        User::query()
            ->select('id')
            ->whereBetween('id', [$this->startUserId, $this->endUserId])
            ->whereHas('userProfile', function ($query) {
                $query->whereNotNull('location');
            })
            ->orderBy('id')
            ->chunkById(250, function ($users) use ($curatedMatchService, $windowStart) {
                foreach ($users as $user) {
                    $curatedMatchService->generateWeeklyCuratedMatchesForUser(
                        (int) $user->id,
                        $windowStart,
                        $this->maxItems,
                        $this->maxDistanceKm
                    );
                }
            });
    }
}
