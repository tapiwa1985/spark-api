<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserDiscoveryPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserDiscoveryPreference>
 */
class UserDiscoveryPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'min_age' => 18,
            'max_age' => 35,
            'max_distance_radius_km' => 50,
            'gender' => 'female',
        ];
    }
}
