<?php

namespace Database\Factories;

use App\Models\ProfileImage;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileImage>
 */
class ProfileImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_url' => fake()->url(),
            'display_order' => rand(0,5),
            'is_display' => true,
            'user_profile_id' => UserProfile::factory()->create(),
            'caption' => fake()->sentence,
        ];
    }
}
