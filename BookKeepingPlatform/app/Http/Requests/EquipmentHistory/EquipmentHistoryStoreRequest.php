<?php

namespace App\Http\Requests\EquipmentHistory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentHistoryStoreRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'equipment_id' => 'required|exists:equipment,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
            'loan_date' => 'required|array|min:1',
            'loan_date.*' => 'required|date_format:Y-m-d',
            'loan_expire_date' => 'required|array|min:1',
            'loan_expire_date.*' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'The equipment is required.',
            'equipment_id.exists' => 'The selected equipment does not exist.',
            'user_ids.required' => 'At least one user is required.',
            'user_ids.*.required' => 'Each user must be selected.',
            'user_ids.*.exists' => 'One or more selected users do not exist.',
            'loan_date.required' => 'At least one loan date is required.',
            'loan_date.*.required' => 'Each loan date must be filled.',
            'loan_date.*.date_format' => 'Each loan date must be in YYYY-MM-DD format.',
            'loan_expire_date.required' => 'At least one loan expiration date is required.',
            'loan_expire_date.*.required' => 'Each expiration date must be filled.',
            'loan_expire_date.*.date_format' => 'Each expiration date must be in YYYY-MM-DD format.',
        ];
    }
}
