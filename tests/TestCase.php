<?php

namespace Tests;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\MachineryParameter;
use App\Models\Research;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function prepareQuantitativeValues(
        Research $research,
        MachineryParameterTypeEnum $parameterType
    ): array {
        return $research->parameters()
            ->whereParameterType($parameterType->value)
            ->whereValueType(MachineryParameterValueTypeEnum::QUANTITATIVE->value)
            ->get()->map(function (MachineryParameter $machineryParameter) {
                return [
                    'parameter_id' => $machineryParameter->id,
                    'value' => $this->faker->randomFloat(2, 0, 100),
                ];
            })->toArray();
    }

    protected function prepareQualityValues(
        Research $research,
        MachineryParameterTypeEnum $parameterType
    ): array {
        return $research->parameters()
            ->whereParameterType($parameterType->value)
            ->whereValueType(MachineryParameterValueTypeEnum::QUALITY->value)
            ->get()->map(function (MachineryParameter $machineryParameter) {
                return [
                    'parameter_id' => $machineryParameter->id,
                    'value' => $this->faker->unique()->word(),
                ];
            })->toArray();
    }
}
