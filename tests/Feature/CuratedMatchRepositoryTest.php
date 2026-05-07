<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\CuratedMatch;
use App\Models\CuratedMatchesWindow;
use App\Repositories\Contracts\CuratedMatchRepositoryInterface;

class CuratedMatchRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CuratedMatchRepositoryInterface $_curatedMatchRepository;

    public function setUp(): void 
    {
        parent::setUp();
        $this->_curatedMatchRepository = app()->make(CuratedMatchRepositoryInterface::class);
    }

    public function testCreateCurateMatch()
    {
        $curatedMatch = User::factory()->create();

        $curatedMatchesWindow = CuratedMatchesWindow::factory()->create();

        $data = [
            'curated_matches_window_id' => $curatedMatchesWindow->id,
            'user_id' => $curatedMatch->id,
            'rank_score' => 4.5,
        ];

        $result = $this->_curatedMatchRepository->create($data);

        $this->assertInstanceOf(CuratedMatch::class, $result);
        $this->assertDatabaseHas('curated_matches', $data);
        $this->assertEquals($result->user_id, $data['user_id']);
        $this->assertEquals($result->rank_score, $data['rank_score']);
        $this->assertEquals($result->curated_matches_window_id, $data['curated_matches_window_id']);
    }
}
