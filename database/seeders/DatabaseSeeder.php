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

        // Generate 50 additional users with random dates (last 3 months)
        // This also creates agencies for agents and bank accounts for all users
        $this->call(UsersSeeder::class);

        // Create agencies and bank accounts for the existing Saudi users
        $this->createRelationshipsForExistingUsers();
    }

    /**
     * Create agencies and bank accounts for existing Saudi users
     */
    private function createRelationshipsForExistingUsers(): void
    {
        // Get all existing users (the Saudi users we created earlier)
        $existingUsers = User::whereIn('national_id', [
            '12345678901234', // Saudi User
            '12345678901235', // Saudi Agent
            '12345678901236'  // Saudi Admin
        ])->get();

        foreach ($existingUsers as $user) {
            // Create bank account for all users
            $this->createBankAccountForUser($user);

            // Create agencies for agent users only
            if ($user->type === 'agent') {
                $this->createAgenciesForAgent($user);
            }
        }
    }

    /**
     * Create bank account for a user
     */
    private function createBankAccountForUser($user): void
    {
        \App\Models\BankAccount::create([
            'user_id' => $user->id,
            'bank_name' => $this->generateBankName(),
            'account_name' => $user->name,
            'bank_address' => $this->generateBankAddress(),
            'IBAN' => $this->generateSaudiIBAN(),
        ]);
    }

    /**
     * Create agencies for an agent user (1 or 2 agencies)
     */
    private function createAgenciesForAgent($agent): void
    {
        // Each agent gets 1 or 2 agencies (random)
        $agencyCount = rand(1, 2);
        
        for ($i = 0; $i < $agencyCount; $i++) {
            \App\Models\Agency::create([
                'user_id' => $agent->id,
                'name' => $this->generateAgencyName($agent->name, $i + 1),
                'number' => $this->generateAgencyNumber(),
                'address' => $this->generateSaudiAddress(),
                'date' => $this->generateAgencyDate(),
            ]);
        }
    }

    /**
     * Generate a Saudi bank name
     */
    private function generateBankName(): string
    {
        $banks = [
            'Al Rajhi Bank',
            'National Commercial Bank (NCB)',
            'Riyad Bank',
            'Saudi British Bank (SABB)',
            'Banque Saudi Fransi',
            'Arab National Bank',
            'Saudi Investment Bank',
            'Alinma Bank',
            'Bank AlJazira',
            'Saudi National Bank'
        ];
        
        return $banks[array_rand($banks)];
    }

    /**
     * Generate a bank address
     */
    private function generateBankAddress(): string
    {
        $cities = ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina', 'Khobar', 'Dhahran'];
        $districts = ['Al Olaya', 'Al Malaz', 'Al Naseem', 'Al Rawdah', 'Al Faisaliyah', 'Al Hamra'];
        
        $city = $cities[array_rand($cities)];
        $district = $districts[array_rand($districts)];
        $street = rand(1, 999);
        
        return "Branch Street {$street}, {$district}, {$city}, Saudi Arabia";
    }

    /**
     * Generate a valid Saudi IBAN
     */
    private function generateSaudiIBAN(): string
    {
        // Saudi IBAN format: SA + 2 check digits + 2 bank code + 18 account number
        $bankCodes = ['10', '20', '30', '40', '50', '60', '70', '80', '90'];
        $bankCode = $bankCodes[array_rand($bankCodes)];
        
        // Generate 18-digit account number
        $accountNumber = str_pad(rand(1, 999999999999999999), 18, '0', STR_PAD_LEFT);
        
        // Calculate check digits (simplified - in real scenario you'd use proper IBAN validation)
        $checkDigits = str_pad(rand(10, 99), 2, '0', STR_PAD_LEFT);
        
        return "SA{$checkDigits}{$bankCode}{$accountNumber}";
    }

    /**
     * Generate a unique agency name
     */
    private function generateAgencyName(string $agentName, int $index): string
    {
        $agencyTypes = ['Real Estate', 'Property', 'Investment', 'Trading', 'Development'];
        $type = $agencyTypes[array_rand($agencyTypes)];
        
        if ($index > 1) {
            return "{$agentName} {$type} Agency {$index}";
        }
        
        return "{$agentName} {$type} Agency";
    }

    /**
     * Generate a unique agency number
     */
    private function generateAgencyNumber(): string
    {
        return 'AG-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) . '-' . date('Y');
    }

    /**
     * Generate a Saudi address
     */
    private function generateSaudiAddress(): string
    {
        $cities = ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina', 'Khobar', 'Dhahran'];
        $districts = ['Al Olaya', 'Al Malaz', 'Al Naseem', 'Al Rawdah', 'Al Faisaliyah', 'Al Hamra'];
        
        $city = $cities[array_rand($cities)];
        $district = $districts[array_rand($districts)];
        $street = rand(1, 999);
        
        return "Street {$street}, {$district}, {$city}, Saudi Arabia";
    }

    /**
     * Generate a random date for agency (within the last 5 years)
     */
    private function generateAgencyDate(): string
    {
        $startDate = strtotime('-5 years');
        $endDate = time();
        $randomTimestamp = rand($startDate, $endDate);
        
        return date('Y-m-d', $randomTimestamp);
    }
}
