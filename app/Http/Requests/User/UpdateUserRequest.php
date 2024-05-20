<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends StoreUserRequest
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
        $rules = parent::rules();

        $rules['email'][count($rules['email']) - 1] =
            Rule::unique('users')->ignore($this->user());

        $rules['new_password'] = ['nullable', Password::defaults()];
        $rules['current_password'] = ['required', 'string', 'current_password', 'confirmed'];

        return $rules;
    }
}
