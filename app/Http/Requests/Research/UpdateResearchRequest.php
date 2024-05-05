<?php

namespace App\Http\Requests\Research;

use Illuminate\Validation\Rule;

class UpdateResearchRequest extends StoreResearchRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('research'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['name'][-1] = Rule::unique('research')->ignore($this->route('research'));

        return $rules;
    }
}
