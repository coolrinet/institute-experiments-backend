<?php

namespace App\Http\Requests\Research;

use App\Models\Machinery;
use App\Models\MachineryParameter;
use App\Models\Research;
use Closure;
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
        $parameterLinkedToMachineryRule = function (string $attribute, mixed $value, Closure $fail) {
            $machinery = Machinery::find($this->validated('machinery_id'));
            foreach ($value as $item) {
                $parameter = MachineryParameter::find($item);

                if ($parameter->machinery_id && $parameter->machinery_id !== $machinery->id) {
                    $fail("The {$attribute} has ids that are not linked to the selected machinery.");
                }
            }
        };

        return [
            'name' => ['required', 'string', 'max:255', 'unique:'.Research::class],
            'description' => ['nullable', 'string'],
            'is_public' => ['required', 'boolean'],
            'participants' => ['present_if:is_public,false', 'array', 'exists:users,id'],
            'machinery_id' => ['required', 'integer', 'exists:machineries,id'],
            'parameters' => [
                'required',
                'array',
                'exists:machinery_parameters,id',
                $parameterLinkedToMachineryRule,
            ],
        ];
    }
}
