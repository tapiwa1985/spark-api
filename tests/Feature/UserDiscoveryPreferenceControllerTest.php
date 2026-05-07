<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Interest;
use App\Models\Language;
use App\Models\Industry;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserDiscoveryPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateUserPreferenceDiscovery()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

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

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'min_age',
                    'max_age',
                    'max_distance_radius_km',
                    'gender',
                    'verified_only',
                    'interests' => [
                        '*' => [
                            'id',
                            'interest_name'
                        ]
                    ],
                    'languages' => [
                        '*' => [
                            'id',
                            'language_name',
                        ]
                    ],
                    'industries' => [
                        '*' => [
                            'id',
                            'industry_name'
                        ]
                    ]
                ]
            ]);

            $response->assertJsonPath('data.min_age', $data['min_age']);
            $response->assertJsonPath('data.max_age', $data['max_age']);
            $response->assertJsonPath('data.gender', $data['gender']);
            $response->assertJsonPath('data.verified_only', $data['verified_only']);
            $response->assertJsonPath('data.max_distance_radius_km', $data['max_distance_radius_km']);

            $response->assertJsonCount(4, 'data.interests');
            $response->assertJsonCount(4, 'data.languages');
            $response->assertJsonCount(4, 'data.industries');
    }

    public function testCreateUserPreferenceWhenMinAgeIsNullAssertStatusUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => '',
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
            'verified_only' => true,
            'interestIds' => $interests->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'min_age' => ['The min age field is required.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenMinAgeIsStringAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 'invalid',
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
            'verified_only' => true,
            'interestIds' => $interests->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'min_age' => ['The min age field must be an integer.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenMaxAgeIsEmptyAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 17,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
            'verified_only' => true,
            'interestIds' => $interests->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'min_age' => ['The min age field must be at least 18.']
                ]
            ]);
    }
}
