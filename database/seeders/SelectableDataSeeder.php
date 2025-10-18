<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubCategoryInput;
use App\Models\SelectableData;

class SelectableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure inputs exist
        if (SubCategoryInput::count() === 0) {
            $this->call(SubCategoryInputSeeder::class);
        }

        // Only add selectable data for inputs of type 'select'
        $selectInputs = SubCategoryInput::where('type', 'select')->get();

        foreach ($selectInputs as $input) {
            // Create 3-6 options per select input
            $count = rand(3, 6);
            SelectableData::factory($count)->create([
                'sub_category_input_id' => $input->id,
            ]);
        }
    }
}


