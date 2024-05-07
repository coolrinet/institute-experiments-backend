<?php

namespace App\Http\Requests\MachineryParameter;

use Illuminate\Validation\Rule;

class UpdateMachineryParameterRequest extends StoreMachineryParameterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('machinery_parameter'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['name'][count($rules['name']) - 1] = Rule::unique('machinery_parameters')
            ->ignore($this->route('machinery_parameter'));

        return $rules;
    }
}
