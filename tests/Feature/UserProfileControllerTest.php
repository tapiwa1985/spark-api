<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
