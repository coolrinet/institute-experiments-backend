<?php

namespace Database\Factories;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
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
            'parameter_type' => fake()->randomElement(MachineryParameterTypeEnum::values()),
            'value_type' => fake()->randomElement(MachineryParameterValueTypeEnum::values()),
            'user_id' => User::factory(),
            'machinery_id' => fake()->randomElement([Machinery::factory(), null]),
        ];
    }
}
