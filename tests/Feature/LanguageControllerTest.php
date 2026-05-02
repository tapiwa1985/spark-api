<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Language;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function testGetLAnguageListAssertStatusOk()
    {
        Language::factory(5)->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/languages')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'language_name',
                        'native_name',
                        'code',
                    ]
                ]
        ]);
    }
}
