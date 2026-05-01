<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\InterestCategory;
use App\Models\Interest;

class InterestCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllInterestCategories(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        $interestCategory = InterestCategory::factory()->create();

        Interest::factory(4)->create([
            'interest_category_id' => $interestCategory->id,
        ]);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/interest-categories')
        ->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                     '*' => [
                         'id',
                         'icon',
                         'interest_category_name',
                         'interests' => [
                             '*' => [
                                 'id',
                                 'interest_name',
                             ],
                         ],
                    ]
                ]
        ]);
    }
}
