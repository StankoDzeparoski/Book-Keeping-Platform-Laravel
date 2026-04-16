<?php

namespace App\Http\Requests\MaintenanceRecord;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MaintenanceRecordStoreRequest extends FormRequest
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
            'description' => 'required|array|min:1',
            'description.*' => 'required|string|max:500',
            'cost' => 'required|integer|min:0',
            'maintenance_date' => 'required|array|min:1',
            'maintenance_date.*' => 'required|date_format:Y-m-d',
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'The equipment is required.',
            'equipment_id.exists' => 'The selected equipment does not exist.',
            'description.required' => 'At least one description is required.',
            'description.*.required' => 'Each description must be filled.',
            'description.*.max' => 'Each description cannot exceed 500 characters.',
            'cost.required' => 'The cost is required.',
            'cost.integer' => 'The cost must be a number.',
            'maintenance_date.required' => 'At least one maintenance date is required.',
            'maintenance_date.*.required' => 'Each date must be filled.',
            'maintenance_date.*.date_format' => 'Each date must be in YYYY-MM-DD format.',
        ];
    }
}
