<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Contracts\MatchRepositoryInterface;
use Illuminate\Database\Seeder;

/**
 * Seeds two users linked by one active {@see \App\Models\UserMatch}, each with a {@see UserProfile}.
 *
 * Run after reference data (e.g. {@see IndustriesTableSeeder}) when using a fixed industry id:
 * `php artisan db:seed --class=MatchedTestUsersSeeder`
 */
class MatchedTestUsersSeeder extends Seeder
{
    /**
     * Creates matched test users, profiles, and a single ACTIVE match row.
     *
     * @return void
     */
    public function run(): void
    {
        $userOne = User::factory()->create([
            'name' => 'Matched Test User One',
            'email' => 'matched.test.user1@example.com',
        ]);

        $userTwo = User::factory()->create([
            'name' => 'Matched Test User Two',
            'email' => 'matched.test.user2@example.com',
        ]);

        $industryId = Industry::query()->value('id');

        UserProfile::factory()
            ->for($userOne)
            ->create($industryId !== null ? ['industry_id' => $industryId] : []);

        UserProfile::factory()
            ->for($userTwo)
            ->create($industryId !== null ? ['industry_id' => $industryId] : []);

        app(MatchRepositoryInterface::class)->create([
            'user_id' => $userOne->id,
            'matched_user_id' => $userTwo->id,
        ]);
    }
}
