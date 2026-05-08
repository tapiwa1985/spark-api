<?php

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use App\Jobs\DispatchWeeklyCuratedMatchesJob;
use App\Services\Contracts\CuratedMatchServiceInterface;

class GenerateWeeklyCuratedMatchesCommand extends Command
{
    protected $signature = 'curated-matches:generate-weekly
        {--start-date= : Period start date (YYYY-MM-DD), defaults to current week start}
        {--chunk-size=1000 : Number of user IDs per dispatched chunk}
        {--max-items=5 : Number of curated matches per user}
        {--max-distance=50 : Maximum discovery distance in km}
        {--queue= : Queue name for worker jobs}
        {--sync : Process inline without dispatching queue jobs}';

    protected $description = 'Generate weekly curated matches for all users with location data.';

    public function handle(CuratedMatchServiceInterface $curatedMatchService): int
    {
        $windowStart = $this->resolveWindowStart();
        $chunkSize = max(100, (int) $this->option('chunk-size'));
        $maxItems = max(1, (int) $this->option('max-items'));
        $maxDistanceKm = max(1, (int) $this->option('max-distance'));
        $queue = $this->option('queue');

        if ($this->option('sync')) {
            $job = new DispatchWeeklyCuratedMatchesJob(
                windowStartIso: $windowStart->toIso8601String(),
                chunkSize: $chunkSize,
                maxItems: $maxItems,
                maxDistanceKm: $maxDistanceKm,
                targetQueue: is_string($queue) ? $queue : null
            );

            $job->handle($curatedMatchService);
            $this->info('Weekly curated matches generated in sync mode.');

            return self::SUCCESS;
        }

        $dispatchJob = new DispatchWeeklyCuratedMatchesJob(
            windowStartIso: $windowStart->toIso8601String(),
            chunkSize: $chunkSize,
            maxItems: $maxItems,
            maxDistanceKm: $maxDistanceKm,
            targetQueue: is_string($queue) ? $queue : null
        );

        if (is_string($queue) && $queue !== '') {
            $dispatchJob->onQueue($queue);
        }

        dispatch($dispatchJob);

        $this->info(sprintf(
            'Queued weekly curated matching pipeline (start: %s, chunk-size: %d, max-items: %d).',
            $windowStart->toDateString(),
            $chunkSize,
            $maxItems
        ));

        return self::SUCCESS;
    }

    private function resolveWindowStart(): CarbonImmutable
    {
        $startDate = $this->option('start-date');

        if (is_string($startDate) && $startDate !== '') {
            return CarbonImmutable::parse($startDate)->startOfDay();
        }

        return CarbonImmutable::now()->startOfWeek();
    }
}
