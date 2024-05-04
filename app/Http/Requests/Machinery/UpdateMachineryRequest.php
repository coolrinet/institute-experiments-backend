<?php

namespace App\Http\Requests\Machinery;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateMachineryRequest extends StoreMachineryRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('machinery'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['name'][-1] = Rule::unique('machineries')
            ->ignore($this->route('machinery'));

        return $rules;
    }
}
