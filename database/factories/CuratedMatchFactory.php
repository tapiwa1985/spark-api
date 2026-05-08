<?php

namespace Database\Factories;

use App\Models\CuratedMatch;
use App\Models\CuratedMatchesWindow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CuratedMatch>
 */
class CuratedMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rank_score' => fake()->randomFloat(2, 0, 100),
            'user_id' => User::factory()->create(),
            'curated_matches_window_id' => CuratedMatchesWindow::factory()->create(),
        ];
    }
}
