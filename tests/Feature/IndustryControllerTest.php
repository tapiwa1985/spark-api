<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Industry;

class IndustryControllerTest extends TestCase
{
    use RefreshDatabase;
    public function testGetIndustryListAssertStatusOk()
    {
        Industry::factory(10)->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/industries')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'industry_name',
                    ]
                ]
        ]);
    }
}
