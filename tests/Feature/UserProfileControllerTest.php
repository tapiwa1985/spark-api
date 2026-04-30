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

    /**
     * It creates a user and profile with a valid request payload.
     */
    public function testCreateUserProfile()
    {
        // Build a valid payload for the registration endpoint.
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => 'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => '1990-12-12',
            'gender' => 'male',
        ];

        // Assert a profile is created and the response mirrors key submitted fields.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'bio' => $request['bio'],
                    'dob' => $request['dob'],
                    'gender' => $request['gender'],
                    'user' => [
                        'name' => $request['name'],
                        'email' => $request['email'],
                    ]
                ]
            ]);
    }

    /**
     * It returns validation errors when the name is missing.
     */
    public function testWhenNameIsEmptyAssertUnprocessable()
    {
        // Name is required, so this payload should fail validation.
        $request = [
            'name' => '',
            'email' => fake()->email(),
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing name.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    /**
     * It returns validation errors when the email is missing.
     */
    public function testWhenEmailIsEmptyAssertUnprocessable()
    {
        // Email is required, so this payload should fail validation.
        $request = [
            'name' => fake()->name(),
            'email' => '',
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing email.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email field is required.'
                    ]
                ]
            ]);
    }

    /**
     * It returns validation errors when the email format is invalid.
     */
    public function testWhenEmailIsNotValidAssertUnprocessable()
    {
        // Invalid email format should be rejected by validation rules.
        $request = [
            'name' => fake()->name(),
            'email' => fake()->word(),
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Confirm the endpoint returns the expected email-format validation message.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    /**
     * It returns validation errors when the email already exists.
     */
    public function testWhenEmailIsNotUniqueAssertUnprocessable()
    {
        // Seed an existing user so we can assert unique email validation.
        $user = User::factory()->create();

        $request = [
            'name' => fake()->name(),
            'email' => $user->email,
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Duplicate email should fail with a uniqueness validation error.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
    }

    /**
     * It returns validation errors when the password is missing.
     */
    public function testWhenPasswordIsEmptyAssertUnprocessable()
    {
        // Password is required, so this payload should fail validation.
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  '',
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]);
    }

    public function testWhenPasswordIsLessThanEightAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'pass1@L',
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field must be at least 8 characters.'
                    ]
                ]
            ]);
    }

    public function testWhePasswordHasNoNumberAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword',
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field must contain at least one number.'
                    ]
                ]
            ]);
    }

    public function testWhenPasswordHasNoSymbolAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP1ssword',
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field must contain at least one symbol.'
                    ]
                ]
            ]);
    }

    public function testWhenPasswordHasNoMixedCaseAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'strongp@ssword',
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field must contain at least one uppercase and one lowercase letter.'
                    ]
                ]
            ]);
    }

    public function testWhenBioIsEmptyAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => '',
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'bio' => [
                        'The bio field is required.'
                    ]
                ]
            ]);
    }

    public function testWhenDobIsEmptyAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => '',
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'dob' => [
                        'The dob field is required.'
                    ]
                ]
            ]);
    }

    public function testWhenDobIsNotValidDateAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => fake()->word(),
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'dob' => [
                        'The dob field must be a valid date.'
                    ]
                ]
            ]);
    }


    public function testWhenDobIsFutureDateAssertUnprocessable()
    {
        $futureDate = Carbon::now()->addDays(10)->format('Y-m-d');

        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => $futureDate,
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'dob' => [
                        'The dob field must be a date before today.'
                    ]
                ]
            ]);
    }

    public function testWhenUserIsBelowLegalAgeAssertUnprocessable()
    {
        $dob = Carbon::now()->subDays(17)->format('Y-m-d');

        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => $dob,
            'gender' => 'male',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'dob' => [
                        'The dob field must be a date before ' . Carbon::now()->subYears(18)->format('Y-m-d') . '.'
                    ]
                ]
            ]);
    }

    public function testWhenGenderIsEmptyAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => '1990-12-12',
            'gender' => '',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'gender' => [
                        'The gender field is required.'
                    ]
                ]
            ]);
    }

    public function testWhenGenderIsNotValidAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' =>  'StrongP@ssword123#!',
            'bio' => fake()->sentence(),
            'dob' => '1990-12-12',
            'gender' => 'invalid',
        ];

        // Verify Laravel returns a validation error for the missing password.
        $this->postJson('/api/v1/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'gender' => [
                        'The selected gender is invalid.'
                    ]
                ]
            ]);
    }

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
