<?php

namespace Database\Factories;

use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Term::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['term', 'privacy', 'faq'];
        $segments = ['user', 'agent'];
        
        return [
            'title' => $this->faker->sentence(rand(3, 8)),
            'content' => $this->faker->paragraphs(rand(3, 8), true),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'type' => $this->faker->randomElement($types),
            'segment' => $this->faker->randomElement($segments),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}
