<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubCategory;
use App\Models\SubCategoryInput;

class SubCategoryInputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure sub categories exist
        if (SubCategory::count() === 0) {
            $this->call(SubCategorySeeder::class);
        }

        // For each subcategory, create 2-5 inputs
        SubCategory::all()->each(function (SubCategory $subCategory) {
            $count = rand(2, 5);
            SubCategoryInput::factory($count)->create([
                'sub_category_id' => $subCategory->id,
            ]);
        });
    }
}


