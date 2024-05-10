<?php

namespace App\Http\Requests\Experiment;

use Illuminate\Validation\Rule;

class UpdateExperimentRequest extends StoreExperimentRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('experiment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['name'][count($rules['name']) - 1] = Rule::unique('experiments')->ignore($this->route('experiment'));

        return $rules;
    }
}
