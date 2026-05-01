<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create interest categories
        $categories = [
            [
                'icon' => 'briefcase',
                'interest_category_name' => 'Career & Business',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'heart',
                'interest_category_name' => 'Lifestyle & Wellness',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'coffee',
                'interest_category_name' => 'Food & Dining',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'plane',
                'interest_category_name' => 'Travel & Adventure',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'music',
                'interest_category_name' => 'Arts & Entertainment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'trophy',
                'interest_category_name' => 'Sports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'book-open',
                'interest_category_name' => 'Learning & Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'globe',
                'interest_category_name' => 'Social & Community',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'gamepad-2',
                'interest_category_name' => 'Gaming & Tech',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'icon' => 'home',
                'interest_category_name' => 'Home & DIY',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('interest_categories')->insert($categories);

        // Get category IDs
        $careerId = DB::table('interest_categories')->where('interest_category_name', 'Career & Business')->value('id');
        $lifestyleId = DB::table('interest_categories')->where('interest_category_name', 'Lifestyle & Wellness')->value('id');
        $foodId = DB::table('interest_categories')->where('interest_category_name', 'Food & Dining')->value('id');
        $travelId = DB::table('interest_categories')->where('interest_category_name', 'Travel & Adventure')->value('id');
        $artsId = DB::table('interest_categories')->where('interest_category_name', 'Arts & Entertainment')->value('id');
        $sportsId = DB::table('interest_categories')->where('interest_category_name', 'Sports')->value('id');
        $learningId = DB::table('interest_categories')->where('interest_category_name', 'Learning & Education')->value('id');
        $socialId = DB::table('interest_categories')->where('interest_category_name', 'Social & Community')->value('id');
        $gamingId = DB::table('interest_categories')->where('interest_category_name', 'Gaming & Tech')->value('id');
        $homeId = DB::table('interest_categories')->where('interest_category_name', 'Home & DIY')->value('id');

        // Create interests
        $interests = [
            // Career & Business
            ['interest_name' => 'Entrepreneurship', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Networking', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Investing', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Public Speaking', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Leadership', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Mentoring', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Startups', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Real Estate', 'interest_category_id' => $careerId, 'created_at' => now(), 'updated_at' => now()],
            
            // Lifestyle & Wellness
            ['interest_name' => 'Meditation', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Yoga', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Mindfulness', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Spa & Relaxation', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Personal Growth', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Journaling', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Mental Health', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Self Care', 'interest_category_id' => $lifestyleId, 'created_at' => now(), 'updated_at' => now()],
            
            // Food & Dining
            ['interest_name' => 'Fine Dining', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Coffee Enthusiast', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Wine Tasting', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Craft Beer', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Cooking', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Baking', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Vegan/Vegetarian', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Foodie', 'interest_category_id' => $foodId, 'created_at' => now(), 'updated_at' => now()],
            
            // Travel & Adventure
            ['interest_name' => 'Beach Vacations', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Mountain Hiking', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Road Trips', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Camping', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Luxury Travel', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Backpacking', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Safari', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Cruises', 'interest_category_id' => $travelId, 'created_at' => now(), 'updated_at' => now()],
            
            // Arts & Entertainment
            ['interest_name' => 'Live Music', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Theatre', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Movies', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Art Galleries', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Photography', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Dancing', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Stand-up Comedy', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Concerts', 'interest_category_id' => $artsId, 'created_at' => now(), 'updated_at' => now()],
            
            // SPORTS (Complete Category)
            // Team Sports
            ['interest_name' => 'Football (Soccer)', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Basketball', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Tennis', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Cricket', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Rugby', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Volleyball', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Baseball', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Hockey', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            
            // Individual Sports
            ['interest_name' => 'Running', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Gym Workouts', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Swimming', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Golf', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Cycling', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Martial Arts', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Boxing', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'CrossFit', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Pilates', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Zumba', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            
            // Outdoor & Adventure Sports
            ['interest_name' => 'Hiking', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Rock Climbing', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Skiing', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Snowboarding', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Surfing', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Skateboarding', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Kayaking', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Fishing', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            
            // Fitness & Wellness
            ['interest_name' => 'Marathon Training', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Triathlon', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Weightlifting', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Calisthenics', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Functional Training', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Spinning', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            
            // Sports Fandom
            ['interest_name' => 'Watching Sports', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Fantasy Sports', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Sports Betting', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Esports', 'interest_category_id' => $sportsId, 'created_at' => now(), 'updated_at' => now()],
            
            // Learning & Education
            ['interest_name' => 'Reading Books', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Online Courses', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Learning Languages', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Science & Tech', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'History', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Philosophy', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Writing', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Podcasts', 'interest_category_id' => $learningId, 'created_at' => now(), 'updated_at' => now()],
            
            // Social & Community
            ['interest_name' => 'Volunteering', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Charity Events', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Meetups', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Brunch', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Cocktail Bars', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Wine Bars', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Dinner Parties', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Clubbing', 'interest_category_id' => $socialId, 'created_at' => now(), 'updated_at' => now()],
            
            // Gaming & Tech
            ['interest_name' => 'Video Games', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Board Games', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Tech Gadgets', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'AI & Machine Learning', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Coding', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'PC Gaming', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Mobile Gaming', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Virtual Reality', 'interest_category_id' => $gamingId, 'created_at' => now(), 'updated_at' => now()],
            
            // Home & DIY
            ['interest_name' => 'Interior Design', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Gardening', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Home Renovation', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Furniture Restoration', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Smart Home', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Plants', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Woodworking', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
            ['interest_name' => 'Painting', 'interest_category_id' => $homeId, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('interests')->insert($interests);
    }
}