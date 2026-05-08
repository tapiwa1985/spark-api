<?php

namespace App\Jobs;

use Carbon\CarbonImmutable;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\Contracts\CuratedMatchServiceInterface;

class DispatchWeeklyCuratedMatchesJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 600;

    public function __construct(
        public readonly string $windowStartIso,
        public readonly int $chunkSize,
        public readonly int $maxItems,
        public readonly int $maxDistanceKm,
        public readonly ?string $targetQueue = null
    ) {
    }

    public function handle(CuratedMatchServiceInterface $curatedMatchService): void
    {
        $windowStart = CarbonImmutable::parse($this->windowStartIso);
        $curatedMatchService->expireActiveWindowsForPeriod($windowStart);

        $bounds = User::query()
            ->whereHas('userProfile', function ($query) {
                $query->whereNotNull('location');
            })
            ->selectRaw('MIN(id) as min_id, MAX(id) as max_id')
            ->first();

        if (!$bounds || $bounds->min_id === null || $bounds->max_id === null) {
            return;
        }

        $minId = (int) $bounds->min_id;
        $maxId = (int) $bounds->max_id;

        for ($start = $minId; $start <= $maxId; $start += $this->chunkSize) {
            $end = min($start + $this->chunkSize - 1, $maxId);

            $job = new ProcessWeeklyCuratedMatchesChunkJob(
                startUserId: $start,
                endUserId: $end,
                windowStartIso: $this->windowStartIso,
                maxItems: $this->maxItems,
                maxDistanceKm: $this->maxDistanceKm
            );

            if ($this->targetQueue !== null && $this->targetQueue !== '') {
                $job->onQueue($this->targetQueue);
            }

            dispatch($job);
        }
    }
}
