<?php

namespace App\Http\Requests\Research;

use App\Models\Research;
use Illuminate\Foundation\Http\FormRequest;

class StoreResearchRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:'.Research::class],
            'description' => ['nullable', 'string'],
            'is_public' => ['required', 'boolean'],
            'participants' => ['present_if:is_public,false', 'array', 'exists:users,id'],
            'machinery_id' => ['required', 'integer', 'exists:machineries,id'],
        ];
    }
}
