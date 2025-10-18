<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SelectableData;
use App\Models\SubCategoryInput;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SelectableData>
 */
class SelectableDataFactory extends Factory
{
    protected $model = SelectableData::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sub_category_input_id' => SubCategoryInput::inRandomOrder()->value('id') ?? SubCategoryInput::factory()->create()->id,
            'label' => $this->faker->words(2, true),
            'value' => (string) $this->faker->unique()->numberBetween(1, 9999),
        ];
    }
}


