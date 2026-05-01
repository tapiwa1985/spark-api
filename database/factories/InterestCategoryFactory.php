<?php

namespace Database\Factories;

use App\Models\InterestCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InterestCategory>
 */
class InterestCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'icon' => fake()->imageUrl(),
            'interest_category_name' => fake()->lexify('???? ???')
        ];
    }
}
