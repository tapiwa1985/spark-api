<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserProfile;

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

        $request = [
            'bio' => fake()->paragraph,
            'dob' => fake()->date(),
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/' . $userProfile->id, $request)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $userProfile->id,
                    'bio' => $request['bio'],
                    'gender' => $request['gender'],
                    'dob' => $request['dob'],
                    'user' => [
                        'id' => $userProfile->user->id,
                        'name' => $userProfile->user->name,
                        'email' => $userProfile->user->email,
                    ],
                ]
        ]);
    }

    public function testUpdateProfileWhenBioIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'bio' => '',
            'dob' => fake()->date(),
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/' . $userProfile->id, $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'bio' => [
                        'The bio field is required.'
                    ]
                ]
        ]);
    }

    public function testUpdateProfileWhenDobIsEmptyAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'bio' => fake()->paragraph(),
            'dob' => '',
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/' . $userProfile->id, $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'dob' => [
                        'The dob field is required.'
                    ]
                ]
        ]);
    }

    public function testUpdateProfileWhenDobIsNotValidAssertUnprocessable()
    {
        $userProfile = UserProfile::factory()->create();

        $token = JWTAuth::fromUser($userProfile->user);

        $request = [
            'bio' => fake()->paragraph(),
            'dob' => 'invalid-date',
            'gender' => 'male'
        ];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/user-profiles/' . $userProfile->id, $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'dob' => [
                        'The dob field must be a valid date.'
                    ]
                ]
        ]);
    }
}
