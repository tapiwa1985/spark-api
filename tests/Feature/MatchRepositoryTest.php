<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserMatch;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\MatchRepositoryInterface;

class MatchRepositoryTest extends TestCase
{
    use RefreshDatabase; 

    private MatchRepositoryInterface $_matchRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_matchRepository = app()->make(MatchRepositoryInterface::class);
    }

    public function testCreateMatch()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $result = $this->_matchRepository->create([
            'user_id' => $user2->id,
            'matched_user_id' => $user1->id
        ]);

        $result->refresh();

        $this->assertInstanceOf(UserMatch::class, $result);
        $this->assertEquals($result->user_id, $user2->id);
        $this->assertEquals($result->matched_user_id, $user1->id);
        $this->assertEquals($result->status, 'ACTIVE');

        $this->assertDatabaseHas('user_matches', [
            'user_id' => $user2->id,
            'matched_user_id' => $user1->id,
            'status' => 'ACTIVE',
        ]);
    }

    public function testGetListOfMatches()
    {
        $user = User::factory()->create();
        $userProfiles = UserProfile::factory(10)->create();

        foreach ($userProfiles as $userProfile) {
            UserMatch::factory(10)->create([
                'user_id' => $user->id,
                'matched_user_id' => $userProfile->user->id,
            ]);
        }

        $result = $this->_matchRepository->getMatchesForUser($user->id);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertInstanceOf(UserProfile::class, $result->get(0));
        $this->assertCount(10, $result);

        foreach($userProfiles as $userProfile) {
            $this->assertTrue($result->contains($userProfile));
        }
    }
}
