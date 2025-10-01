<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // generate saudi data for user
        User::factory()->create([
            'name' => 'Saudi User',
            'national_id' => '12345678901234',
            'phone' => '+966500000000',
            'address' => 'Riyadh, SA',
            'summary' => 'I am a Saudi user',
            'type' => 'user',
            'is_active' => true,
            'password' => Hash::make('123456'),
        ]);

        // generate saudi data for agent
        User::factory()->create([
            'name' => 'Saudi Agent',
            'national_id' => '12345678901235',
            'phone' => '+966500000001',
            'address' => 'Riyadh, SA',
            'summary' => 'I am a Saudi agent',
            'type' => 'agent',
            'is_active' => true,
            'password' => Hash::make('123456'),
        ]);

        // generate saudi data for admin
        User::factory()->create([
            'name' => 'Saudi Admin',
            'national_id' => '12345678901236',
            'phone' => '+966500000002',
            'address' => 'Riyadh, SA',
            'summary' => 'I am a Saudi admin',
            'type' => 'admin',
            'is_active' => true,
            'password' => Hash::make('123456'),
        ]);

        // Seed agencies for agent users
        $this->call(AgencySeeder::class);

        // Seed bank accounts for all users
        $this->call(BankAccountSeeder::class);
    }
}
