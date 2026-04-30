<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = [
            // Technology & IT
            ['industry_name' => 'Software Development', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Cyber Security', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Data Science & Analytics', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Cloud Computing', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'IT Support & Infrastructure', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Artificial Intelligence', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'DevOps', 'created_at' => now(), 'updated_at' => now()],
            
            // Finance & Banking
            ['industry_name' => 'Investment Banking', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Private Equity', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Asset Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Financial Advisory', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Commercial Banking', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Insurance', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Fintech', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Accounting & Auditing', 'created_at' => now(), 'updated_at' => now()],
            
            // Legal
            ['industry_name' => 'Corporate Law', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Litigation', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Human Rights Law', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Property Law', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Tax Law', 'created_at' => now(), 'updated_at' => now()],
            
            // Healthcare & Medical
            ['industry_name' => 'Medicine', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Nursing', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Pharmaceuticals', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Dentistry', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Psychology & Therapy', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Veterinary', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Public Health', 'created_at' => now(), 'updated_at' => now()],
            
            // Engineering & Construction
            ['industry_name' => 'Civil Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Mechanical Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Electrical Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Chemical Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Architecture', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Construction Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Quantity Surveying', 'created_at' => now(), 'updated_at' => now()],
            
            // Business & Consulting
            ['industry_name' => 'Management Consulting', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Strategy Consulting', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Human Resources', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Recruitment', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Business Development', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Project Management', 'created_at' => now(), 'updated_at' => now()],
            
            // Marketing & Creative
            ['industry_name' => 'Digital Marketing', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Brand Strategy', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Graphic Design', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Content Creation', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'UX/UI Design', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Video Production', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Public Relations', 'created_at' => now(), 'updated_at' => now()],
            
            // Sales & Real Estate
            ['industry_name' => 'Real Estate', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Property Development', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Enterprise Sales', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Retail Management', 'created_at' => now(), 'updated_at' => now()],
            
            // Education & Academia
            ['industry_name' => 'University Teaching', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'School Teaching', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Educational Technology', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Research', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Academic Administration', 'created_at' => now(), 'updated_at' => now()],
            
            // Mining & Resources (relevant for Gauteng/South Africa)
            ['industry_name' => 'Mining Engineering', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Geology', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Metallurgy', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Oil & Gas', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Renewable Energy', 'created_at' => now(), 'updated_at' => now()],
            
            // Logistics & Supply Chain
            ['industry_name' => 'Supply Chain Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Logistics Coordination', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Freight & Shipping', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Warehouse Management', 'created_at' => now(), 'updated_at' => now()],
            
            // Government & Public Sector
            ['industry_name' => 'Public Administration', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Policy Development', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Diplomacy', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Municipal Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'State Owned Entity', 'created_at' => now(), 'updated_at' => now()],
            
            // Media & Journalism
            ['industry_name' => 'Journalism', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Broadcasting', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Publishing', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Social Media Management', 'created_at' => now(), 'updated_at' => now()],
            
            // Hospitality & Events
            ['industry_name' => 'Hotel Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Event Planning', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Restaurant Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Tourism', 'created_at' => now(), 'updated_at' => now()],
            
            // Arts & Entertainment
            ['industry_name' => 'Fine Arts', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Music', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Performing Arts', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Gallery Curation', 'created_at' => now(), 'updated_at' => now()],
            
            // Sports & Fitness
            ['industry_name' => 'Professional Sports', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Sports Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Fitness Training', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Wellness Coaching', 'created_at' => now(), 'updated_at' => now()],
            
            // Non-Profit & Social Impact
            ['industry_name' => 'Non-Profit Management', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Social Work', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Fundraising', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Community Development', 'created_at' => now(), 'updated_at' => now()],
            
            // Entrepreneurship
            ['industry_name' => 'Startup Founder', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Small Business Owner', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Franchise Owner', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'E-commerce', 'created_at' => now(), 'updated_at' => now()],
            
            // Trades & Technical
            ['industry_name' => 'Plumbing', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Electrical', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Carpentry', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Welding', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Automotive', 'created_at' => now(), 'updated_at' => now()],
            
            // Other
            ['industry_name' => 'Military', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Law Enforcement', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Fire & Rescue', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Aviation', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Maritime', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Student/Master\'s', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Student/PhD', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Retired Executive', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Homemaker', 'created_at' => now(), 'updated_at' => now()],
            ['industry_name' => 'Other', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('industries')->insert($industries);
    }
}