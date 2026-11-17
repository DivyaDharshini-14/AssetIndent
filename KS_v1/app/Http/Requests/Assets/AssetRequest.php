<?php

namespace App\Http\Requests\Assets;

use Illuminate\Foundation\Http\FormRequest;
use App\AssetStatus;
use App\AssetCondition;
use App\AssetOperationalStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
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
         
        $assetId = $this->route('asset');

        // If route-model binding is used
        if (is_object($assetId)) {
            $assetId = $assetId->id;
        }

        $isAssigned = $this->input('status') === 'assigned'
            || $this->input('status') === AssetStatus::ASSIGNED->value;

       return [

            /* ================= ASSET DETAILS ================= */
            'asset_name'        => 'required|string|max:255',
            'asset_structure' => 'required|in:single,group',
            'items_count' => 'required_if:asset_structure,group|nullable|integer|min:1',
            'category_id' => 'required|exists:asset_categories,id',
            'sub_category_id' => 'required|exists:asset_categories,id',
            'asset_type_id'     => 'required|exists:asset_types,id',
            'serial_no'         => [
            'required',
            'string',
            'max:255',
            Rule::unique('assets', 'serial_no')->ignore($assetId),
        ],
            'asset_code'        => [
            'required',
            'string',
            'max:255',
            Rule::unique('assets', 'asset_code')->ignore($assetId),
        ],
            
            'status'            => ['required', new Enum(AssetStatus::class)],
            'condition'         => ['required', new Enum(AssetCondition::class)],
            'operational_status' => ['required', new Enum(AssetOperationalStatus::class)],
            'location_id'       => 'nullable|exists:branch_locations,id',
            'branch_id' => 'required|exists:branches,id',
            'company_id'=>'required|exists:companies,id',

            'to_company_id' => [$isAssigned ? 'required' : 'nullable', 'exists:companies,id'],
            'to_branch' => [
                $isAssigned ? 'required' : 'nullable',
                'exists:branches,id',
                Rule::when($isAssigned, ['different:branch_id']),
            ],
            'to_location' => 'nullable|exists:branch_locations,id',

            /* ================= PURCHASE & WARRANTY ================= */
            'purchase_date'     => 'required|date_format:Y-m-d',
            'purchase_cost'     => 'required|numeric|min:0',
            'supplier'          => 'required|integer|exists:vendors,id', 
            'brand_id'          => 'required|integer|exists:brands,id',
            'invoice_details' => 'nullable|string',

            /* ================= DEPRECIATION ================= */
          'depreciation_method' => 'required|in:no_depreciation,straight_line,reducing_balance',

'userful_life' => 'nullable|required_if:depreciation_method,straight_line|numeric|min:1',

'salvage_value' => [
    'nullable',
    'required_if:depreciation_method,straight_line,reducing_balance',
    'numeric',
    'min:0',
    function ($attr, $value, $fail) {
        if ($this->depreciation_method === 'reducing_balance' && $value > 100) {
            $fail('Rate of depreciation cannot exceed 100%.');
        }
    }
],
            /* ================= DESCRIPTION ================= */
            'description'       => 'nullable|string|max:2000',
        ];
    }
}
