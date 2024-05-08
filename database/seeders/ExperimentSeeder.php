<?php

namespace Database\Seeders;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\Experiment;
use App\Models\Research;
use Illuminate\Database\Seeder;

class ExperimentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $researchList = Research::all();

        foreach ($researchList as $researchItem) {
            $participants = $researchItem->participants()->get()
                ->add($researchItem->author);

            Experiment::factory(5)
                ->for($researchItem)
                ->recycle($participants)
                ->create();
        }

        $experiments = Experiment::all();

        foreach ($experiments as $experiment) {
            $researchParameters = $experiment->research->parameters;

            foreach ($researchParameters as $researchParameter) {
                $parameterType = $researchParameter->parameter_type;
                $valueType = $researchParameter->value_type;

                switch (true) {
                    case $parameterType === MachineryParameterTypeEnum::INPUT->value
                    && $valueType === MachineryParameterValueTypeEnum::QUANTITATIVE->value:
                        $experiment->quantitativeInputs()->attach($researchParameter->id, [
                            'value' => fake()->randomFloat(2, 100, 10000),
                        ]);
                        break;
                    case $parameterType === MachineryParameterTypeEnum::OUTPUT->value
                    && $valueType === MachineryParameterValueTypeEnum::QUANTITATIVE->value:
                        $experiment->quantitativeOutputs()->attach($researchParameter->id, [
                            'value' => fake()->randomFloat(2, 100, 10000),
                        ]);
                        break;
                    case $parameterType === MachineryParameterTypeEnum::INPUT->value
                    && $valueType === MachineryParameterValueTypeEnum::QUALITY->value:
                        $experiment->qualityInputs()->attach($researchParameter->id, [
                            'value' => fake()->word(),
                        ]);
                        break;
                    case $parameterType === MachineryParameterTypeEnum::OUTPUT->value
                    && $valueType === MachineryParameterValueTypeEnum::QUALITY->value:
                        $experiment->qualityOutputs()->attach($researchParameter->id, [
                            'value' => fake()->word(),
                        ]);
                        break;
                }
            }
        }
    }
}
