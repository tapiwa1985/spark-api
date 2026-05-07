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

    public function testCreateUserPreferenceWhenMaxDistanceRadiusIsEmptyAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => '',
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
                    'max_distance_radius_km' => ['The max distance radius km field is required.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenMaxDistanceRadiusIsStringAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 'invalid',
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
                    'max_distance_radius_km' => ['The max distance radius km field must be an integer.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenGenderIsEmptyAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => '',
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
                    'gender' => ['The gender field is required.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenGenderIsNotValidAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'invalid',
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
                    'gender' => ['The selected gender is invalid.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenInterestIdsIsNotAnArrayAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'male',
            'verified_only' => true,
            'interestIds' => '',
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'interestIds' => ['The interest ids field must be an array.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenInterestIdsIsEmptyAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'male',
            'verified_only' => true,
            'interestIds' => [],
            'languageIds' => $languages->pluck('id'),
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'interestIds' => ['The interest ids field must have at least 1 items.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenLanguageIdsIsNotArrayAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'male',
            'verified_only' => true,
            'interestIds' => $industries->pluck('id'),
            'languageIds' => '',
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'languageIds' => ['The language ids field must be an array.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenLanguageIdsIsEmptyAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'male',
            'verified_only' => true,
            'interestIds' => $industries->pluck('id'),
            'languageIds' => [],
            'industryIds' => $industries->pluck('id'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'languageIds' => ['The language ids field must have at least 1 items.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenIndustryIdsIsNotArrayAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'male',
            'verified_only' => true,
            'interestIds' => $industries->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => 'invalif',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'industryIds' => ['The industry ids field must be an array.']
                ]
            ]);
    }

    public function testCreateUserPreferenceWhenIndustryIdsIsEmptyAssertUnprocessable()
    {
        $user =  User::factory()->create();

        $interests = Interest::factory(4)->create();
        $languages = Language::factory(4)->create();
        $industries = Industry::factory(4)->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'user_id' => $user->id,
            'min_age' => 27,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'male',
            'verified_only' => true,
            'interestIds' => $industries->pluck('id'),
            'languageIds' => $languages->pluck('id'),
            'industryIds' => []
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', '/api/v1/user-discovery-preferences', $data)
            ->assertUnprocessable()
            ->assertJson([
                'errors' => [
                    'industryIds' => ['The industry ids field must have at least 1 items.']
                ]
            ]);
    }
}
