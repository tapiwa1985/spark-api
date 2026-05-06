<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery as m;
use App\Models\UserDiscoveryPreference;
use App\Services\UserDiscoveryPreferenceService;
use App\Repositories\Contracts\UserDiscoveryPreferenceRepositoryInterface;

class UserDiscoveryPreferenceServiceUnitTest extends TestCase
{
    public function testCreateUserPreferences()
    {
        $data = [
            'user_id' => 1,
            'min_age' => 18,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
            'verified_only' => true,
            'interestIds' => [1],
            'languageIds' => [1],
            'industryIds' => [1],
        ];

        $interest = m::mock(Interest::class)->makePartial();
        $interest->id = 1;

        $language = m::mock(Language::class)->makePartial();
        $language->id = 1;

        $industry = m::mock(Industry::class)->makePartial();
        $industry->id = 1;

        $userDiscoveryPreferenceMock = m::mock(UserDiscoveryPreference::class)->makePartial();
        $userDiscoveryPreferenceMock->user_id = $data['user_id'];
        $userDiscoveryPreferenceMock->min_age = $data['min_age'];
        $userDiscoveryPreferenceMock->max_age = $data['max_age'];
        $userDiscoveryPreferenceMock->max_distance_radius_km = $data['max_distance_radius_km'];
        $userDiscoveryPreferenceMock->gender = $data['gender'];
        $userDiscoveryPreferenceMock->verified_ony = $data['verified_only'];

        $repoMock = $this->mock(UserDiscoveryPreferenceRepositoryInterface::class, function ($mock) use($data, $userDiscoveryPreferenceMock) {
            $mock->shouldReceive('create')
                ->once()
                ->with($data)
                ->andReturn($userDiscoveryPreferenceMock);
        });

        $service = new UserDiscoveryPreferenceService($repoMock);

        $result = $service->create($data);

        $this->assertInstanceOf(UserDiscoveryPreference::class, $result);
        $this->assertEquals($result->max_age, $data['max_age']);
        $this->assertEquals($result->min_age, $data['min_age']);
        $this->assertEquals($result->max_distance_radius_km, $data['max_distance_radius_km']);
        $this->assertEquals($result->gender, $data['gender']);
    }
}
