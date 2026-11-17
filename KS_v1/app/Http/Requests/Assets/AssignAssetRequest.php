<?php

namespace App\Http\Requests\Assets;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignAssetRequest extends FormRequest
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
           'asset_id' => ['required', 'exists:assets,id'],

            'assign_date' => ['required', 'date'],

            'assigned_to' => ['required', Rule::in(['branch', 'employee'])],

            'to_company_id' => ['required', 'exists:companies,id'],
            'from_company_id'=> ['nullable', 'exists:companies,id'],
            'from_branch' => ['nullable', 'exists:branches,id'],
            'from_location' => ['nullable', 'exists:branch_locations,id'],

            'to_branch' => [
                'required',
                'exists:branches,id',
                'different:from_branch',
            ],

            'to_location' => [
                'nullable',
                'exists:branch_locations,id',
                'different:from_location',
            ],

            'assiged_emoloyee' => [
                'nullable',
                'required_if:assigned_to,employee',
                'exists:users,id',
            ],

            'notes' => ['nullable', 'string'],
        ];
    }

     /**
     * Optional: cleaner validation messages
     */
    public function messages(): array
    {
        return [
            'assiged_emoloyee.required_if' =>
                'Employee is required when assigning to employee.',
        ];
    }
}
