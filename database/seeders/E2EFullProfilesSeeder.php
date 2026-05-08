<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds high-volume E2E users with complete profiles for mobile testing.
 */
class E2EFullProfilesSeeder extends Seeder
{
    private const TOTAL_USERS = 9000;
    private const BATCH_SIZE = 300;
    private const EMAIL_DOMAIN = 'e2e.spark.test';
    private const PASSWORD = 'Password123!';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::disableQueryLog();

        $this->call([
            IndustriesTableSeeder::class,
            InterestsTableSeeder::class,
            LanguagesTableSeeder::class,
        ]);

        $industryIds = DB::table('industries')->pluck('id')->all();
        $interestIds = DB::table('interests')->pluck('id')->all();
        $languageIds = DB::table('languages')->pluck('id')->all();

        if (count($industryIds) === 0 || count($interestIds) === 0 || count($languageIds) === 0) {
            $this->command?->warn('Reference data missing (industries/interests/languages). Aborting E2E seed.');
            return;
        }

        $hashedPassword = Hash::make(self::PASSWORD);

        for ($start = 1; $start <= self::TOTAL_USERS; $start += self::BATCH_SIZE) {
            $end = min($start + self::BATCH_SIZE - 1, self::TOTAL_USERS);
            $users = [];
            $now = now();

            for ($i = $start; $i <= $end; $i++) {
                $users[] = [
                    'name' => sprintf('E2E User %05d', $i),
                    'email' => sprintf('e2e.user.%05d@%s', $i, self::EMAIL_DOMAIN),
                    'email_verified_at' => $now,
                    'password' => $hashedPassword,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            User::query()->insert($users);

            $insertedUsers = User::query()
                ->whereBetween('email', [
                    sprintf('e2e.user.%05d@%s', $start, self::EMAIL_DOMAIN),
                    sprintf('e2e.user.%05d@%s', $end, self::EMAIL_DOMAIN),
                ])
                ->orderBy('email')
                ->get(['id']);

            $interestPivotRows = [];
            $languagePivotRows = [];
            $preferenceRows = [];
            $discoveryPreferenceInterestRows = [];
            $discoveryPreferenceLanguageRows = [];
            $discoveryPreferenceIndustryRows = [];

            foreach ($insertedUsers as $user) {
                $lat = fake()->randomFloat(6, -34.200000, -22.100000);
                $lng = fake()->randomFloat(6, 16.450000, 32.950000);

                $profile = UserProfile::query()->create([
                    'user_id' => $user->id,
                    'bio' => fake()->paragraph(),
                    'dob' => fake()->dateTimeBetween('-50 years', '-21 years')->format('Y-m-d'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'job_title' => fake()->jobTitle(),
                    'industry_id' => fake()->randomElement($industryIds),
                    'location' => Point::makeGeodetic($lat, $lng),
                ]);

                $selectedInterestIds = fake()->randomElements($interestIds, fake()->numberBetween(3, 5));
                foreach ($selectedInterestIds as $interestId) {
                    $interestPivotRows[] = [
                        'user_profile_id' => $profile->id,
                        'interest_id' => $interestId,
                    ];
                }

                $selectedLanguageIds = fake()->randomElements($languageIds, fake()->numberBetween(1, 3));
                foreach ($selectedLanguageIds as $languageId) {
                    $languagePivotRows[] = [
                        'user_profile_id' => $profile->id,
                        'language_id' => $languageId,
                    ];
                }

                $preferenceRows[] = [
                    'user_id' => $user->id,
                    'min_age' => fake()->numberBetween(21, 30),
                    'max_age' => fake()->numberBetween(31, 45),
                    'verified_only' => false,
                    'max_distance_radius_km' => fake()->randomElement([30, 50, 80]),
                    'gender' => fake()->randomElement(['male', 'female', 'both']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('interest_user_profile')->insert($interestPivotRows);
            DB::table('language_user_profile')->insert($languagePivotRows);
            DB::table('user_discovery_preferences')->insert($preferenceRows);

            $preferences = DB::table('user_discovery_preferences')
                ->whereIn('user_id', $insertedUsers->pluck('id'))
                ->get(['id']);

            foreach ($preferences as $preference) {
                foreach (fake()->randomElements($interestIds, 2) as $interestId) {
                    $discoveryPreferenceInterestRows[] = [
                        'interest_id' => $interestId,
                        'user_discovery_preference_id' => $preference->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                foreach (fake()->randomElements($languageIds, 1) as $languageId) {
                    $discoveryPreferenceLanguageRows[] = [
                        'language_id' => $languageId,
                        'user_discovery_preference_id' => $preference->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $discoveryPreferenceIndustryRows[] = [
                    'industry_id' => fake()->randomElement($industryIds),
                    'user_discovery_preference_id' => $preference->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('discovery_pref_interests')->insert($discoveryPreferenceInterestRows);
            DB::table('discovery_pref_languages')->insert($discoveryPreferenceLanguageRows);
            DB::table('discovery_pref_industries')->insert($discoveryPreferenceIndustryRows);

            $this->command?->info(sprintf('Seeded E2E users %d to %d', $start, $end));
        }

        $this->command?->info('E2E seed complete: 9000 users with full profiles.');
        $this->command?->info(sprintf('Login password for all E2E users: %s', self::PASSWORD));
        $this->command?->info(sprintf('Example email: %s', sprintf('e2e.user.%05d@%s', 1, self::EMAIL_DOMAIN)));
    }
}
