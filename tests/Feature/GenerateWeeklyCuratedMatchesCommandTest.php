<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\DispatchWeeklyCuratedMatchesJob;

class GenerateWeeklyCuratedMatchesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testCommandDispatchesWeeklyCuratedMatchesPipeline(): void
    {
        Queue::fake();

        $this->artisan('curated-matches:generate-weekly', [
            '--chunk-size' => 500,
            '--max-items' => 5,
            '--max-distance' => 40,
        ])->assertSuccessful();

        Queue::assertPushed(DispatchWeeklyCuratedMatchesJob::class, function (DispatchWeeklyCuratedMatchesJob $job) {
            return $job->chunkSize === 500
                && $job->maxItems === 5
                && $job->maxDistanceKm === 40;
        });
    }
}
