<?php

namespace Database\Factories;

use App\Enums\MachineryParameterType;
use App\Enums\MachineryParameterValueType;
use App\Models\Machinery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MachineryParameter>
 */
class MachineryParameterFactory extends Factory
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
            'parameter_type' => fake()->randomElement(MachineryParameterType::values()),
            'value_type' => fake()->randomElement(MachineryParameterValueType::values()),
            'user_id' => User::factory(),
            'machinery_id' => fake()->randomElement([Machinery::factory(), null]),
        ];
    }
}
