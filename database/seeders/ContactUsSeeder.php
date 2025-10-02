<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactUs;
use App\Models\SocialMedia;
class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ContactUs::create([
            'name' => 'Contact Us',
            'phone' => '1234567890',
        ]);

        SocialMedia::create([
            'platform' => 'Facebook',
            'url' => 'https://www.facebook.com',
        ]);

        SocialMedia::create([
            'platform' => 'Instagram',
            'url' => 'https://www.instagram.com',
        ]);
    }
}
