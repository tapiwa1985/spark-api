<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateUserProfile()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

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

    public function testWhenNameIsEmptyAssertUnprocessable()
    {
        $request = [
            'name' => '',
            'email' => fake()->email(),
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

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

    public function testWhenEmailIsEmptyAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => '',
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

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

    public function testWhenEmailIsNotValidAssertUnprocessable()
    {
        $request = [
            'name' => fake()->name(),
            'email' => fake()->word(),
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

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

    public function testWhenEmailIsNotUniqueAssertUnprocessable()
    {
        $user = User::factory()->create();

        $request = [
            'name' => fake()->name(),
            'email' => $user->email,
            'password' => fake()->password(),
            'bio' => fake()->sentence(),
            'dob' => fake()->date(),
            'gender' => 'male',
        ];

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
}
