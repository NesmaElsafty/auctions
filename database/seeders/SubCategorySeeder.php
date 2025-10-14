<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are categories; if none, seed them first
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        // For each category, create 3-6 sub categories
        Category::all()->each(function (Category $category) {
            $count = rand(3, 6);
            SubCategory::factory($count)->create([
                'category_id' => $category->id,
            ]);
        });
    }
}
