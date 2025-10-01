<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users (including agents and admins)
        $users = User::all();

        foreach ($users as $user) {
            // Each user gets exactly 1 bank account
            BankAccount::create([
                'user_id' => $user->id,
                'bank_name' => $this->generateBankName(),
                'account_name' => $user->name,
                'bank_address' => $this->generateBankAddress(),
                'IBAN' => $this->generateSaudiIBAN(),
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
}
