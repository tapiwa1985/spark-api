<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserMatch;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\ChatMessage;
use App\Models\Interest;
use App\Models\Industry;
use App\Models\Language;
use App\Models\Like;
use App\Models\UserDiscoveryPreference;
use App\Models\DiscoveryPrefInterest;
use App\Models\DiscoveryPrefLanguage;
use App\Models\DiscoveryPrefIndustry;
use Illuminate\Support\Collection;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Clickbar\Magellan\Data\Geometries\Point;
use Carbon\Carbon;

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

    public function testGetChatMessagesForMatch()
    {
        $match = UserMatch::factory()->create();

        $chatMessages = ChatMessage::factory(5)->create(['user_match_id' => $match->id]);

        $userMatch = $this->_matchRepository->find($match->id);

        $this->assertInstanceOf(Collection::class, $userMatch->chatMessages);
        $this->assertCount(5, $userMatch->chatMessages);
        $this->assertInstanceOf(ChatMessage::class, $userMatch->chatMessages->get(0));
    }

    public function testUnmatchUser()
    {
        $match = UserMatch::factory()->create();

        $messages = ChatMessage::factory(5)->create([
            'user_match_id' => $match->id,
            'sender_id' => $match->user_id,
        ]);

        $this->_matchRepository->unmatch($match->user_id, $match->id);

        $this->assertSoftDeleted($match);
        $this->assertDatabaseHas('user_matches', [
            'unmatched_by_user_id' => $match->user_id,
        ]);
        foreach ($messages as $message) {
            $this->assertSoftDeleted($message);
        }
    }

    public function testGetPotentialMatchesReturnsCandidatesWhenUserHasNoPreferences()
    {
        $searcher = $this->createSearcher();
        $nearby = $this->createCandidate(['gender' => 'female']);
        $faraway = $this->createCandidate(['gender' => 'female'], $this->capeTownPoint());

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $this->assertCount(1, $results);
        $this->assertEquals($nearby->id, $results[0]->user_id);
    }

    public function testGetPotentialMatchesExcludesSearcher()
    {
        $searcher = $this->createSearcher();
        $other = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertNotContains($searcher->id, $userIds);
        $this->assertContains($other->id, $userIds);
    }

    public function testGetPotentialMatchesExcludesProfilesWithoutLocation()
    {
        $searcher = $this->createSearcher();
        $withLocation = $this->createCandidate();
        $withoutLocation = $this->createCandidate([], null);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertContains($withLocation->id, $userIds);
        $this->assertNotContains($withoutLocation->id, $userIds);
    }

    public function testGetPotentialMatchesExcludesAlreadyLikedUsers()
    {
        $searcher = $this->createSearcher();
        $liked = $this->createCandidate();
        $other = $this->createCandidate();

        Like::create([
            'user_id' => $searcher->id,
            'liked_user_id' => $liked->id,
        ]);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertNotContains($liked->id, $userIds);
        $this->assertContains($other->id, $userIds);
    }

    public function testGetPotentialMatchesExcludesUsersWithActiveMatches()
    {
        $searcher = $this->createSearcher();
        $alreadyMatched = $this->createCandidate();
        $stillAvailable = $this->createCandidate();

        UserMatch::factory()->create([
            'user_id' => $searcher->id,
            'matched_user_id' => $alreadyMatched->id,
            'status' => UserMatch::USER_MATCH_STATUS_ACTIVE,
        ]);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertNotContains($alreadyMatched->id, $userIds);
        $this->assertContains($stillAvailable->id, $userIds);
    }

    public function testGetPotentialMatchesRespectsLimit()
    {
        $searcher = $this->createSearcher();

        for ($i = 0; $i < 5; $i++) {
            $this->createCandidate();
        }

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            3
        );

        $this->assertCount(3, $results);
    }

    public function testGetPotentialMatchesUsesPreferenceDistanceOverParameter()
    {
        $searcher = $this->createSearcher();
        $this->givePreferences($searcher, ['max_distance_radius_km' => 5]);

        $closeCandidate = $this->createCandidate([], $this->jhbNearbyPoint());
        $farCandidate = $this->createCandidate([], $this->capeTownPoint());

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            10000,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertContains($closeCandidate->id, $userIds);
        $this->assertNotContains($farCandidate->id, $userIds);
    }

    public function testGetPotentialMatchesAppliesAgePreferenceFilter()
    {
        $searcher = $this->createSearcher();
        $this->givePreferences($searcher, [
            'min_age' => 25,
            'max_age' => 30,
        ]);

        $tooYoung = $this->createCandidate([
            'dob' => Carbon::today()->subYears(20)->toDateString(),
        ]);
        $inRange = $this->createCandidate([
            'dob' => Carbon::today()->subYears(28)->toDateString(),
        ]);
        $tooOld = $this->createCandidate([
            'dob' => Carbon::today()->subYears(40)->toDateString(),
        ]);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertContains($inRange->id, $userIds);
        $this->assertNotContains($tooYoung->id, $userIds);
        $this->assertNotContains($tooOld->id, $userIds);
    }

    public function testGetPotentialMatchesAppliesGenderPreferenceFilter()
    {
        $searcher = $this->createSearcher();
        $this->givePreferences($searcher, ['gender' => 'female']);

        $female = $this->createCandidate(['gender' => 'female']);
        $male = $this->createCandidate(['gender' => 'male']);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertContains($female->id, $userIds);
        $this->assertNotContains($male->id, $userIds);
    }

    public function testGetPotentialMatchesGenderBothAllowsAllGenders()
    {
        $searcher = $this->createSearcher();
        $this->givePreferences($searcher, ['gender' => 'both']);

        $female = $this->createCandidate(['gender' => 'female']);
        $male = $this->createCandidate(['gender' => 'male']);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertContains($female->id, $userIds);
        $this->assertContains($male->id, $userIds);
    }

    public function testGetPotentialMatchesAppliesVerifiedOnlyPreference()
    {
        $searcher = $this->createSearcher();
        $this->givePreferences($searcher, ['verified_only' => true]);

        $verified = $this->createCandidate();
        $unverified = $this->createCandidate([], null, ['email_verified_at' => null]);
        $unverified->update(['location' => $this->jhbNearbyPoint()]);

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $userIds = collect($results)->pluck('user_id')->all();
        $this->assertContains($verified->id, $userIds);
        $this->assertNotContains($unverified->id, $userIds);
    }

    public function testGetPotentialMatchesPreferenceInterestsBoostMatchScore()
    {
        $searcher = $this->createSearcher();
        $sharedInterest = Interest::factory()->create();
        $this->givePreferences($searcher, [], [
            'interestIds' => [$sharedInterest->id],
        ]);

        $candidateWithShared = $this->createCandidate();
        $candidateWithShared->userProfile->interests()->attach($sharedInterest->id);

        $candidateWithoutShared = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $byUser = collect($results)->keyBy('user_id');

        $this->assertEquals(1, $byUser[$candidateWithShared->id]->shared_interests);
        $this->assertEquals(0, $byUser[$candidateWithoutShared->id]->shared_interests);
        $this->assertGreaterThan(
            (float) $byUser[$candidateWithoutShared->id]->match_score,
            (float) $byUser[$candidateWithShared->id]->match_score
        );
    }

    public function testGetPotentialMatchesPreferenceLanguagesBoostMatchScore()
    {
        $searcher = $this->createSearcher();
        $sharedLanguage = Language::factory()->create();
        $this->givePreferences($searcher, [], [
            'languageIds' => [$sharedLanguage->id],
        ]);

        $candidateWithShared = $this->createCandidate();
        $candidateWithShared->userProfile->languages()->attach($sharedLanguage->id);

        $candidateWithoutShared = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $byUser = collect($results)->keyBy('user_id');

        $this->assertEquals(1, $byUser[$candidateWithShared->id]->shared_languages);
        $this->assertEquals(0, $byUser[$candidateWithoutShared->id]->shared_languages);
        $this->assertGreaterThan(
            (float) $byUser[$candidateWithoutShared->id]->match_score,
            (float) $byUser[$candidateWithShared->id]->match_score
        );
    }

    public function testGetPotentialMatchesPreferenceIndustriesBoostMatchScore()
    {
        $searcher = $this->createSearcher();
        $preferredIndustry = Industry::factory()->create();
        $this->givePreferences($searcher, [], [
            'industryIds' => [$preferredIndustry->id],
        ]);

        $candidateInIndustry = $this->createCandidate(['industry_id' => $preferredIndustry->id]);
        $candidateOutOfIndustry = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $byUser = collect($results)->keyBy('user_id');

        $this->assertEquals(1, $byUser[$candidateInIndustry->id]->same_industry);
        $this->assertEquals(0, $byUser[$candidateOutOfIndustry->id]->same_industry);
        $this->assertGreaterThan(
            (float) $byUser[$candidateOutOfIndustry->id]->match_score,
            (float) $byUser[$candidateInIndustry->id]->match_score
        );
    }

    public function testGetPotentialMatchesFallsBackToSearcherProfileWhenNoPreferences()
    {
        $sharedInterest = Interest::factory()->create();
        $sharedLanguage = Language::factory()->create();
        $sharedIndustry = Industry::factory()->create();

        $searcher = $this->createSearcher(['industry_id' => $sharedIndustry->id]);
        $searcher->userProfile->interests()->attach($sharedInterest->id);
        $searcher->userProfile->languages()->attach($sharedLanguage->id);

        $similarCandidate = $this->createCandidate(['industry_id' => $sharedIndustry->id]);
        $similarCandidate->userProfile->interests()->attach($sharedInterest->id);
        $similarCandidate->userProfile->languages()->attach($sharedLanguage->id);

        $unrelatedCandidate = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $byUser = collect($results)->keyBy('user_id');

        $this->assertEquals(1, $byUser[$similarCandidate->id]->shared_interests);
        $this->assertEquals(1, $byUser[$similarCandidate->id]->shared_languages);
        $this->assertEquals(1, $byUser[$similarCandidate->id]->same_industry);
        $this->assertEquals(0, $byUser[$unrelatedCandidate->id]->shared_interests);
        $this->assertEquals(0, $byUser[$unrelatedCandidate->id]->shared_languages);
        $this->assertEquals(0, $byUser[$unrelatedCandidate->id]->same_industry);
        $this->assertGreaterThan(
            (float) $byUser[$unrelatedCandidate->id]->match_score,
            (float) $byUser[$similarCandidate->id]->match_score
        );
    }

    public function testGetPotentialMatchesOrdersByMatchScoreDescending()
    {
        $sharedInterest = Interest::factory()->create();

        $searcher = $this->createSearcher();
        $this->givePreferences($searcher, [], [
            'interestIds' => [$sharedInterest->id],
        ]);

        $strong = $this->createCandidate();
        $strong->userProfile->interests()->attach($sharedInterest->id);
        $weak = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $this->assertGreaterThanOrEqual(2, count($results));
        $this->assertEquals($strong->id, $results[0]->user_id);
    }

    public function testGetPotentialMatchesReturnsExpectedShape()
    {
        $searcher = $this->createSearcher();
        $candidate = $this->createCandidate();

        $results = $this->_matchRepository->getPotentialMatches(
            $searcher->id,
            $this->jhbLat,
            $this->jhbLng,
            50,
            50
        );

        $this->assertNotEmpty($results);
        $row = $results[0];

        $expectedKeys = [
            'match_score', 'distance_meters', 'shared_interests', 'shared_languages',
            'same_industry', 'user_id', 'name', 'email', 'profile_id', 'bio', 'dob',
            'gender', 'job_title', 'industry_id', 'location', 'industry',
            'images', 'interests', 'languages',
        ];
        foreach ($expectedKeys as $key) {
            $this->assertObjectHasProperty($key, $row);
        }
        $this->assertEquals($candidate->id, $row->user_id);
    }

    private float $jhbLat = -26.2041;
    private float $jhbLng = 28.0473;

    private function jhbPoint(): Point
    {
        return Point::makeGeodetic($this->jhbLat, $this->jhbLng);
    }

    private function jhbNearbyPoint(): Point
    {
        return Point::makeGeodetic(-26.2050, 28.0480);
    }

    private function capeTownPoint(): Point
    {
        return Point::makeGeodetic(-33.9249, 18.4241);
    }

    /**
     * Creates the user performing the search with a verified account, JHB location,
     * a deterministic dob, and an industry assigned. Override profile or user attrs as needed.
     */
    private function createSearcher(array $profileOverrides = [], array $userOverrides = []): User
    {
        return $this->createUserWithProfile(
            array_merge([
                'gender' => 'male',
                'dob' => Carbon::today()->subYears(28)->toDateString(),
            ], $profileOverrides),
            $userOverrides,
            $this->jhbPoint()
        );
    }

    /**
     * Creates a candidate user with a profile near JHB by default.
     * Pass null as $location to leave the profile location unset.
     */
    private function createCandidate(
        array $profileOverrides = [],
        ?Point $location = null,
        array $userOverrides = []
    ): User {
        $point = $location;
        if (func_num_args() < 2) {
            $point = $this->jhbNearbyPoint();
        }

        return $this->createUserWithProfile(
            array_merge([
                'gender' => 'female',
                'dob' => Carbon::today()->subYears(28)->toDateString(),
            ], $profileOverrides),
            $userOverrides,
            $point
        );
    }

    private function createUserWithProfile(array $profileOverrides, array $userOverrides, ?Point $location): User
    {
        $user = User::factory()->create($userOverrides);

        $profileAttrs = array_merge([
            'user_id' => $user->id,
        ], $profileOverrides);

        if ($location !== null) {
            $profileAttrs['location'] = $location;
        }

        UserProfile::factory()->create($profileAttrs);

        return $user->fresh();
    }

    /**
     * Creates a discovery preference row for the user with optional pivot rows
     * for languages, interests, and industries.
     */
    private function givePreferences(User $user, array $overrides = [], array $pivots = []): UserDiscoveryPreference
    {
        $preference = UserDiscoveryPreference::factory()->create(array_merge([
            'user_id' => $user->id,
            'min_age' => 18,
            'max_age' => 100,
            'max_distance_radius_km' => 50,
            'gender' => 'both',
            'verified_only' => false,
        ], $overrides));

        foreach ($pivots['interestIds'] ?? [] as $interestId) {
            DiscoveryPrefInterest::create([
                'interest_id' => $interestId,
                'user_discovery_preference_id' => $preference->id,
            ]);
        }
        foreach ($pivots['languageIds'] ?? [] as $languageId) {
            DiscoveryPrefLanguage::create([
                'language_id' => $languageId,
                'user_discovery_preference_id' => $preference->id,
            ]);
        }
        foreach ($pivots['industryIds'] ?? [] as $industryId) {
            DiscoveryPrefIndustry::create([
                'industry_id' => $industryId,
                'user_discovery_preference_id' => $preference->id,
            ]);
        }

        return $preference;
    }
}
