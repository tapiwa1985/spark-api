<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CuratedMatch;
use App\Models\CuratedMatchesWindow;

class CuratedMatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsAuthenticatedUsersActiveCuratedMatches(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $window = CuratedMatchesWindow::factory()->create([
            'user_id' => $user->id,
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->startOfWeek()->addWeek(),
            'status' => 'ACTIVE',
            'max_items' => 5,
        ]);

        $candidateA = User::factory()->hasUserProfile()->create();
        $candidateB = User::factory()->hasUserProfile()->create();
        $candidateC = User::factory()->hasUserProfile()->create();

        CuratedMatch::factory()->create([
            'curated_matches_window_id' => $window->id,
            'user_id' => $candidateA->id,
            'rank_score' => 99.9,
        ]);

        CuratedMatch::factory()->create([
            'curated_matches_window_id' => $window->id,
            'user_id' => $candidateB->id,
            'rank_score' => 82.4,
        ]);

        CuratedMatch::factory()->create([
            'curated_matches_window_id' => $window->id,
            'user_id' => $candidateC->id,
            'rank_score' => 71.0,
        ]);

        $otherWindow = CuratedMatchesWindow::factory()->create([
            'user_id' => $otherUser->id,
            'status' => 'ACTIVE',
        ]);

        CuratedMatch::factory()->create([
            'curated_matches_window_id' => $otherWindow->id,
            'user_id' => User::factory()->hasUserProfile()->create()->id,
            'rank_score' => 100.0,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->get('/api/v1/curated-matches')->assertOk();

        $response->assertJsonCount(3, 'data');
        $response->assertJsonPath('data.0.rank_score', 99.9);
        $response->assertJsonPath('data.1.rank_score', 82.4);
        $response->assertJsonPath('data.2.rank_score', 71);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'rank_score',
                    'curated_matches_window_id',
                    'user_id',
                    'profile' => [
                        'id',
                        'bio',
                        'dob',
                        'gender',
                        'job_title',
                        'industry',
                        'user',
                    ],
                ],
            ],
        ]);
    }

    public function testIndexRequiresAuthentication(): void
    {
        $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/v1/curated-matches')->assertUnauthorized();
    }
}
