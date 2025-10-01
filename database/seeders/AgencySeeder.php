<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all agent users
        $agentUsers = User::where('type', 'agent')->get();

        foreach ($agentUsers as $agent) {
            // Each agent gets 1 or 2 agencies (random)
            $agencyCount = rand(1, 2);
            
            for ($i = 0; $i < $agencyCount; $i++) {
                Agency::create([
                    'user_id' => $agent->id,
                    'name' => $this->generateAgencyName($agent->name, $i + 1),
                    'number' => $this->generateAgencyNumber(),
                    'address' => $this->generateSaudiAddress(),
                    'date' => $this->generateDate(),
                ]);
            }
        }
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
     * Generate a random date within the last 5 years
     */
    private function generateDate(): string
    {
        $startDate = strtotime('-5 years');
        $endDate = time();
        $randomTimestamp = rand($startDate, $endDate);
        
        return date('Y-m-d', $randomTimestamp);
    }
}
