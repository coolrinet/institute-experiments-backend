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
        return $this->user()->can('create', [Experiment::class, $this->route('research')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ruleForParameter = function (MachineryParameterTypeEnum $parameterType, MachineryParameterValueTypeEnum $valueType) {
            return [
                'required',
                'integer',
                'exists:machinery_parameters,id',
                'exists:research_machinery_parameters,parameter_id',
                function (string $attribute, mixed $value, Closure $fail) use ($parameterType, $valueType) {
                    $parameter = MachineryParameter::find($value);
                    $isParameterTypeCorrect =
                        $parameter->parameter_type === $parameterType->value;
                    $isValueTypeCorrect =
                        $parameter->value_type === $valueType->value;
                    if (! ($isParameterTypeCorrect && $isValueTypeCorrect)) {
                        $fail("The {$attribute} has invalid parameter ids.");
                    }
                },
            ];
        };

        return [
            'name' => ['required', 'string', 'max:255', 'unique:'.Experiment::class],
            'date' => ['required', 'date'],
            'quantitative_inputs' => ['required', 'array'],
            'quantitative_inputs.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::INPUT,
                MachineryParameterValueTypeEnum::QUANTITATIVE
            ),
            'quantitative_inputs.value' => ['required', 'decimal:0,2'],
            'quality_inputs' => ['required', 'array'],
            'quality_inputs.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::INPUT,
                MachineryParameterValueTypeEnum::QUALITY
            ),
            'quality_inputs.value' => ['required', 'string', 'max:255'],
            'quantitative_outputs' => ['required', 'array'],
            'quantitative_outputs.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::INPUT,
                MachineryParameterValueTypeEnum::QUANTITATIVE
            ),
            'quantitative_outputs.value' => ['required', 'decimal:0,2'],
            'quality_outputs' => ['required', 'array'],
            'quality_outputs.parameter_id' => $ruleForParameter(
                MachineryParameterTypeEnum::INPUT,
                MachineryParameterValueTypeEnum::QUALITY
            ),
            'quality_outputs.value' => ['required', 'string', 'max:255'],
        ];
    }
}
