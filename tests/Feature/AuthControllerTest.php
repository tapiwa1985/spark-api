<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

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

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'token_type',
                     'expires_in',
        ]);
    }

    /**
     * Test user login with empty email field.
      *
      * @return void
     */
    public function testUserLoginWhenEmailIsEmptyAssertUnprocessableEntity()
    {
        $response = $this->postJson('/api/v1/login', [
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
        $response = $this->postJson('/api/v1/login', [
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

        $response = $this->postJson('/api/v1/login', [
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

        $response = $this->postJson('/api/v1/login', [
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
}
