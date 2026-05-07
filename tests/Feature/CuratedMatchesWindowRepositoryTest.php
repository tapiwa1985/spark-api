<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\CuratedMatchesWindow;
use App\Repositories\Contracts\CuratedMatchesWindowRepositoryInterface;

class CuratedMatchesWindowRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CuratedMatchesWindowRepositoryInterface $_curatedMatchesWindowRepository;

    public function setUp(): void 
    {
        parent::setUp();
        $this->_curatedMatchesWindowRepository = app()->make(CuratedMatchesWindowRepositoryInterface::class);
    }

    public function testCreateCuratedMatchesWindow()
    {
        $user = User::factory()->create();

        $data = [
            'starts_at' => fake()->dateTime(),
            'ends_at' => fake()->dateTime(),
            'user_id' => $user->id,
            'max_items' => rand(1, 5),
            'status' => 'ACTIVE',
        ];

        $result = $this->_curatedMatchesWindowRepository->create($data);

        $this->assertDatabaseHas('curated_matches_windows', $data);
        $this->assertInstanceOf(CuratedMatchesWindow::class, $result);
        $this->assertEquals($result->user_id, $data['user_id']);
        $this->assertEquals($result->ends_at, $data['ends_at']);
        $this->assertEquals($result->starts_at, $data['starts_at']);
        $this->assertEquals($result->max_items, $data['max_items']);
        $this->assertEquals($result->status, $data['status']);
    }
}
