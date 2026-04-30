<?php

namespace Database\Factories;

use App\Models\Interest;
use App\Models\InterestCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interest>
 */
class InterestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'interest_name' => fake()->lexify('??? ???'),
            'interest_category_id' => InterestCategory::factory()->create(),
        ];
    }
}
