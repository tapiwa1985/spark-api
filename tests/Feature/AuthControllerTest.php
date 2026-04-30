<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user login functionality.
     *
     * @return void
     */
    public function testUserLogin()
    {
        $user = User::factory()->create();

        UserProfile::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'token_type',
                     'expires_in',
                     'user_profile' => [
                        'id',
                        'bio',
                        'dob',
                        'gender',
                        'user' => [
                            'id',
                            'name',
                            'email',
                        ],
                     ]
        ]);
    }

    /**
     * Test user login with empty email field.
      *
      * @return void
     */
    public function testUserLoginWhenEmailIsEmptyAssertUnprocessableEntity()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.']
                ]
        ]);
    }

    /**
     * Test user login with invalid email format.
      *
      * @return void
     */
    public function testUserLoginWhenEmailIsNotValidAssertUnprocessableEntity()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field must be a valid email address.']
                ]
        ]);
    }

    public function testUserLoginWithIncorrectCredentialsAssertUnauthorized()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Unauthorized'
        ]);
    }

    public function testUserLoginWithNoPasswordAssertUnprocessableEntity()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => '',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'errors' => [
                         'password' => ['The password field is required.']
                     ]
        ]);
    }

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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
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
        $this->postJson('/api/v1/auth/register', $request)
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'gender' => [
                        'The selected gender is invalid.'
                    ]
                ]
            ]);
    }
}
