<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 60 terms with created_at between today and 3 months ago
        Term::factory(60)->create();
        
        // Optional: Create some specific terms with known data
        $specificTerms = [
            [
                'title' => 'Terms of Service',
                'content' => 'These are the general terms of service for our platform. By using our services, you agree to these terms.',
                'is_active' => true,
                'type' => 'term',
                'segment' => 'user',
                'created_at' => Carbon::now()->subDays(90),
                'updated_at' => Carbon::now()->subDays(30),
            ],
            [
                'title' => 'Privacy Policy',
                'content' => 'This privacy policy explains how we collect, use, and protect your personal information.',
                'is_active' => true,
                'type' => 'privacy',
                'segment' => 'user',
                'created_at' => Carbon::now()->subDays(85),
                'updated_at' => Carbon::now()->subDays(25),
            ],
            [
                'title' => 'Agent Agreement',
                'content' => 'Terms and conditions for agents using our platform to conduct auctions.',
                'is_active' => true,
                'type' => 'term',
                'segment' => 'user',
                'created_at' => Carbon::now()->subDays(80),
                'updated_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Frequently Asked Questions',
                'content' => 'Common questions and answers about our auction platform and services.',
                'is_active' => true,
                'type' => 'faq',
                'segment' => 'user',
                'created_at' => Carbon::now()->subDays(75),
                'updated_at' => Carbon::now()->subDays(15),
            ],
        ];

        foreach ($specificTerms as $term) {
            Term::create($term);
        }
    }
}