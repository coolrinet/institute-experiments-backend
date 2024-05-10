<?php

namespace App\Http\Requests\Experiment;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\Experiment;
use App\Models\MachineryParameter;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreExperimentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [Experiment::class, $this->route('research')->id]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ruleForParameter = function (
            MachineryParameterTypeEnum $parameterType,
            MachineryParameterValueTypeEnum $valueType
        ): array {
            return [
                'required',
                'integer',
                'exists:machinery_parameters,id',
                'exists:research_machinery_parameter,parameter_id',
                function (string $attribute, mixed $value, Closure $fail) use ($parameterType, $valueType) {
                    $parameter = MachineryParameter::find($value);
                    $isParameterTypeCorrect =
                        $parameter->parameter_type === $parameterType->value;
                    $isValueTypeCorrect =
                        $parameter->value_type === $valueType->value;
                    if (! ($isParameterTypeCorrect && $isValueTypeCorrect)) {
                        $fail("The {$attribute} has invalid parameter id.");
                    }
                },
            ];
        };

        return [
            'name' => ['required', 'string', 'max:255', 'unique:'.Experiment::class],
            'date' => ['required', 'date'],
            'quantitative_inputs' => ['present', 'array'],
            'quantitative_inputs.*.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::INPUT,
                MachineryParameterValueTypeEnum::QUANTITATIVE
            ),
            'quantitative_inputs.*.value' => ['required', 'decimal:0,3'],
            'quality_inputs' => ['present', 'array'],
            'quality_inputs.*.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::INPUT,
                MachineryParameterValueTypeEnum::QUALITY
            ),
            'quality_inputs.*.value' => ['required', 'string', 'max:255'],
            'quantitative_outputs' => ['present', 'array'],
            'quantitative_outputs.*.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::OUTPUT,
                MachineryParameterValueTypeEnum::QUANTITATIVE
            ),
            'quantitative_outputs.*.value' => ['required', 'decimal:0,3'],
            'quality_outputs' => ['present', 'array'],
            'quality_outputs.*.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::OUTPUT,
                MachineryParameterValueTypeEnum::QUALITY
            ),
            'quality_outputs.*.value' => ['required', 'string', 'max:255'],
        ];
    }
}
