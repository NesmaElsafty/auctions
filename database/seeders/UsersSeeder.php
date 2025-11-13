<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agency;
use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 50 users with random creation dates between today and last 3 months
        $users = User::factory(50)->create([
            'created_at' => function () {
                // Generate random date between today and 3 months ago
                $endDate = now();
                $startDate = now()->subMonths(3);
                
                // Generate random timestamp between start and end dates
                $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
                
                return \Carbon\Carbon::createFromTimestamp($randomTimestamp);
            },
            'updated_at' => function () {
                // Generate random date between today and 3 months ago
                $endDate = now();
                $startDate = now()->subMonths(3);
                
                // Generate random timestamp between start and end dates
                $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
                
                return \Carbon\Carbon::createFromTimestamp($randomTimestamp);
            }
        ]);

        // Create bank accounts for the 50 base users
        foreach ($users as $user) {
            $this->createBankAccountForUser($user);
        }

        // Generate additional users with specific types and random dates
        $this->generateUsersByType();
    }

    /**
     * Generate users with specific types and random creation dates
     */
    private function generateUsersByType(): void
    {
        $userTypes = ['user', 'admin'];
        $usersPerType = [30, 5]; // 30 users, 5 admins

        foreach ($userTypes as $index => $type) {
            $count = $usersPerType[$index];
            
            for ($i = 0; $i < $count; $i++) {
                $user = User::factory()->create([
                    'type' => $type,
                    'is_active' => $this->generateRandomActiveStatus(),
                    'summary' => $this->generateSummary($type),
                    'created_at' => $this->generateRandomDate(),
                    'updated_at' => $this->generateRandomDate(),
                ]);

                // Create bank account for all users (user, agent, admin)
                $this->createBankAccountForUser($user);

            }
        }
    }

    /**
     * Generate random creation date between today and 3 months ago
     */
    private function generateRandomDate(): \Carbon\Carbon
    {
        $endDate = now();
        $startDate = now()->subMonths(3);
        
        $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
        
        return \Carbon\Carbon::createFromTimestamp($randomTimestamp);
    }

    /**
     * Generate random active status (80% active, 20% inactive)
     */
    private function generateRandomActiveStatus(): bool
    {
        return rand(1, 100) <= 80; // 80% chance of being active
    }

    /**
     * Generate summary based on user type
     */
    private function generateSummary(string $type): string
    {
        $summaries = [
            'user' => [
                'I am a regular user looking for great deals.',
                'Interested in participating in auctions.',
                'New to the platform, exploring features.',
                'Looking for quality products at good prices.',
                'Active buyer in various categories.'
            ],
            'admin' => [
                'Platform administrator ensuring smooth operations.',
                'System admin managing user accounts and content.',
                'Technical administrator maintaining platform stability.',
                'Content moderator ensuring quality and compliance.',
                'Operations manager overseeing daily activities.'
            ]
        ];

        $typeSummaries = $summaries[$type] ?? $summaries['user'];
        
        return $typeSummaries[array_rand($typeSummaries)];
    }

    /**
     * Create bank account for a user
     */
    private function createBankAccountForUser(User $user): void
    {
        BankAccount::create([
            'user_id' => $user->id,
            'bank_name' => $this->generateBankName(),
            'account_name' => $user->name,
            'bank_address' => $this->generateBankAddress(),
            'IBAN' => $this->generateSaudiIBAN(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    }

    /**

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
