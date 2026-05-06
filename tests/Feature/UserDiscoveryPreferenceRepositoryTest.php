<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Interest;
use App\Models\Language;
use App\Models\Industry;
use App\Models\User;
use App\Models\UserDiscoveryPreference;
use App\Repositories\Contracts\UserDiscoveryPreferenceRepositoryInterface;

class UserDiscoveryPreferenceRepositoryTest extends TestCase
{
    private UserDiscoveryPreferenceRepositoryInterface $_userPreferenceRepository;

    public function setUp(): void 
    {
        parent::setUp();

        $this->_userPreferenceRepository = app()->make(UserDiscoveryPreferenceRepositoryInterface::class);
    }

    public function testCreateUserPreference()
    {
        $user = User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $data = [
            'user_id' => $user->id,
            'min_age' => 18,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
            'verified_only' => true,
            'interestIds' => $interests->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $result = $this->_userPreferenceRepository->create($data);

        $this->assertInstanceOf(UserDiscoveryPreference::class, $result);
        $this->assertEquals($result->max_age, $data['max_age']);
        $this->assertEquals($result->min_age, $data['min_age']);
        $this->assertEquals($result->max_distance_radius_km, $data['max_distance_radius_km']);
        $this->assertEquals($result->gender, $data['gender']);
    
        $this->assertDatabaseHas('user_discovery_preferences', [
            'min_age' => $data['min_age'],
            'max_age' => $data['max_age'],
            'max_distance_radius_km' => $data['max_distance_radius_km'],
            'gender' => 'female',
            'verified_only' => true
        ]);

        foreach ($languages->pluck('id') as $languageId) {
            $this->assertDatabaseHas('discovery_pref_languages', [
                'user_discovery_preference_id' => $result->id,
                'language_id' => $languageId
            ]);
        }

        foreach ($interests->pluck('id') as $interestId) {
            $this->assertDatabaseHas('discovery_pref_interests', [
                'user_discovery_preference_id' => $result->id,
                'interest_id' => $interestId
            ]);
        }

        foreach ($industries->pluck('id') as $industryId) {
            $this->assertDatabaseHas('discovery_pref_industries', [
                'user_discovery_preference_id' => $result->id,
                'industry_id' => $industryId
            ]);
        }
    }

    public function testUpdateUserPreferences()
    {
        $user = User::factory()->create();

        $userDiscoveryPreference = UserDiscoveryPreference::factory()
            ->create(['user_id' => $user->id]);

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $data = [
            'user_id' => $user->id,
            'min_age' => 18,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
            'verified_only' => true,
            'interestIds' => $interests->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $result = $this->_userPreferenceRepository->update($userDiscoveryPreference->id, $data);

        $this->assertDatabaseHas('user_discovery_preferences', [
            'min_age' => $data['min_age'],
            'max_age' => $data['max_age'],
            'max_distance_radius_km' => $data['max_distance_radius_km'],
            'gender' => 'female',
            'verified_only' => true
        ]);

        foreach ($languages->pluck('id') as $languageId) {
            $this->assertDatabaseHas('discovery_pref_languages', [
                'user_discovery_preference_id' => $result->id,
                'language_id' => $languageId
            ]);
        }

        foreach ($interests->pluck('id') as $interestId) {
            $this->assertDatabaseHas('discovery_pref_interests', [
                'user_discovery_preference_id' => $result->id,
                'interest_id' => $interestId
            ]);
        }

        foreach ($industries->pluck('id') as $industryId) {
            $this->assertDatabaseHas('discovery_pref_industries', [
                'user_discovery_preference_id' => $result->id,
                'industry_id' => $industryId
            ]);
        }
    }
}
