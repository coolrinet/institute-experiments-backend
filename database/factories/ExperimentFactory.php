<?php

namespace Database\Factories;

use App\Models\Research;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Experiment>
 */
class ExperimentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'date' => fake()->date(),
            'research_id' => Research::factory(),
            'user_id' => User::factory(),
        ];
    }
}
