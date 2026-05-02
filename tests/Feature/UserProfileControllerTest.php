<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserProfile;
use App\Models\Industry;
use App\Models\Interest;
use App\Models\Language;

/**
 * Feature tests for the user registration/profile API endpoint.
 */
class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateUserProfile()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $industry = Industry::factory()->create();

        $request = [
            'job_title' => fake()->jobTitle(),
            'industry_id' => $industry->id,
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles', $request)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $userProfile->id,
                    'job_title' => $request['job_title'],
                    'industry' => [
                        'id' => $request['industry_id'],
                        'industry_name' => $industry->industry_name,
                    ],
                    'gender' => $request['gender'],
                    'user' => [
                        'id' => $userProfile->user->id,
                        'name' => $userProfile->user->name,
                        'email' => $userProfile->user->email,
                    ],
                ]
        ]);
    }

    public function testUpdateProfileWhenJobTitleIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);
        $industry = Industry::factory()->create();

        $request = [
            'job_title' => '',
            'industry_id' => $industry->id,
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'job_title' => [
                        'The job title field is required.'
                    ]
                ]
        ]);
    }

    public function testUpdateProfileWhenIndustryIdIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'job_title' => fake()->jobTitle(),
            'industry_id' => '',
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'industry_id' => [
                        'The industry id field is required.'
                    ]
                ]
        ]);
    }

    public function testUpdateProfileWhenIndustryIdIsNotValidAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);
        $industry = Industry::factory()->create();
        $request = [
            'job_title' => fake()->jobTitle(),
            'industry_id' => 9000000,
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'industry_id' => [
                        'The selected industry id is invalid.'
                    ]
                ]
        ]);
    }

    public function testUpdateUserProfileWhenUserDoesNotOwnProfileAssertForbidden()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser(User::factory()->create());

        $industry = Industry::factory()->create();

        $request = [
            'job_title' => fake()->jobTitle(),
            'industry_id' => $industry->id,
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles', $request)
            ->assertStatus(403);
    }

    public function testAddInterestsToUserProfile()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $interest1 = Interest::factory()->create();
        $interest2 = Interest::factory()->create();

        $request = [
            'interest_ids' => [$interest1->id, $interest2->id],
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/interests', $request)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $userProfile->id,
                    'interests' => [
                        ['id' => $interest1->id, 'interest_name' => $interest1->interest_name],
                        ['id' => $interest2->id, 'interest_name' => $interest2->interest_name],
                    ],
                ]
        ]);
    }

    public function testAddInterestsToUserProfileWhenUserDoesNotOwnProfileAssertForbidden()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser(User::factory()->create());

        $interest1 = Interest::factory()->create();
        $interest2 = Interest::factory()->create();

        $request = [
            'interest_ids' => [$interest1->id, $interest2->id],
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/interests', $request)
            ->assertStatus(403);
    }

    public function testAddInterestsToUserProfileWhenInterestIdIsInvalidAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'interest_ids' => [999999, 888888],
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/interests', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'interest_ids.0' => ['The selected interest_ids.0 is invalid.'],
                    'interest_ids.1' => ['The selected interest_ids.1 is invalid.'],
                ]
        ]);
    }

    public function testUpdateUserBio()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'bio' => fake()->paragraph(),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/bio', $request)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $userProfile->id,
                    'bio' => $request['bio'],
                ]
        ]);
    }

    public function testUpdateUserBioWhenUserDoesNotOwnProfileAssertForbidden()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser(User::factory()->create());

        $request = [
            'bio' => fake()->paragraph(),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/bio', $request)
            ->assertStatus(403);
    }

    public function testUpdateUserBioWhenBioIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'bio' => '',
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/bio', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'bio' => ['The bio field is required.'],
                ]
        ]);
    }

    public function testUpdateUserBioWhenBioExceedsMaxLengthAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'bio' => str_repeat('a', 501),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/bio', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'bio' => ['The bio field must not be greater than 500 characters.'],
                ]
        ]);
    }

    public function testUpdateUserProfileLocation()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/location', $request)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                     'id' => $userProfile->id,
                    'job_title' => $userProfile->job_title,
                    'industry' => [
                        'id' => $userProfile->industry->id,
                        'industry_name' => $userProfile->industry->industry_name,
                    ],
                    'gender' => $userProfile->gender,
                    'user' => [
                        'id' => $userProfile->user->id,
                        'name' => $userProfile->user->name,
                        'email' => $userProfile->user->email,
                    ],
                    'location' => [
                        'type' => 'Point',
                        'coordinates' => [$request['longitude'], $request['latitude']],
                    ],
                ]
            ]);
    }

    public function testUpdateUserProfileLocationWhenLatitudeIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'latitude' => '',
            'longitude' => fake()->longitude(),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/location', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' =>  [
                    'latitude' => ['The latitude field is required.']
                ]
        ]);
    }

    public function testUpdateUserProfileLocationWhenLatitudeIsNotValidAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'latitude' => -3000,
            'longitude' => fake()->longitude(),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/location', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' =>  [
                    'latitude' => ['The latitude field must be between -90 and 90.']
                ]
        ]);
    }

    public function testUpdateUserProfileLocationWhenLongitudeIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'latitude' => fake()->latitude(),
            'longitude' => '',
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/location', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' =>  [
                    'longitude' => ['The longitude field is required.']
                ]
        ]);
    }

    public function testUpdateUserProfileWhenLongitudeIsNotValidAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'latitude' => fake()->latitude(),
            'longitude' => -800000,
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/location', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' =>  [
                    'longitude' => ['The longitude field must be between -180 and 180.']
                ]
        ]);
    }

    public function testUpdateUserProfileLocationWhenUserDoesNotOwnProfileAssertForbidden()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser(User::factory()->create());

        $request = [
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/user-profiles/location', $request)
            ->assertStatus(403);
    }

    public function testAddLanguagesToUserProfile()
    {
        $userProfile = UserProfile::factory()->create();
        $token = JWTAuth::fromUser($userProfile->user);

        $language1 = Language::factory()->create();
        $language2 = Language::factory()->create();

        $request = [
            'language_ids' => [$language1->id, $language2->id],
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/languages', $request)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $userProfile->id,
                    'languages' => [
                        ['id' => $language1->id, 'language_name' => $language1->language_name],
                        ['id' => $language2->id, 'language_name' => $language2->language_name],
                    ],
                ]
        ]);
    }

}
