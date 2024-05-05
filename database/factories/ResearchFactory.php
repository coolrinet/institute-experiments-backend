<?php

namespace Database\Factories;

use App\Models\Machinery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Research>
 */
class ResearchFactory extends Factory
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
            'description' => fake()->optional()->text(),
            'is_public' => fake()->boolean(),
            'machinery_id' => Machinery::factory(),
            'author_id' => User::factory(),
        ];
    }
}
