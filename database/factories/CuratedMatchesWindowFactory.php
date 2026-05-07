<?php

namespace Database\Factories;

use App\Models\CuratedMatchesWindow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CuratedMatchesWindow>
 */
class CuratedMatchesWindowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'starts_at' => fake()->dateTime(),
            'ends_at' => fake()->dateTime(),
            'max_items' => rand(1, 5),
            'status' => 'ACTIVE',
            'user_id' => User::factory()->create()
        ];
    }
}
