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
}
