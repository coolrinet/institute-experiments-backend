<?php

namespace App\Http\Requests\MachineryParameter;

use App\Enums\MachineryParameterTypeEnum;
use App\Enums\MachineryParameterValueTypeEnum;
use App\Models\MachineryParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMachineryParameterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:'.MachineryParameter::class],
            'parameter_type' => ['required', Rule::in(MachineryParameterTypeEnum::values())],
            'value_type' => ['required', Rule::in(MachineryParameterValueTypeEnum::values())],
            'machinery_id' => ['nullable', 'integer', 'exists:machineries,id'],
        ];
    }
}
