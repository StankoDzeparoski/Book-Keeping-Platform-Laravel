<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EquipmentUpdateRequest extends FormRequest
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
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'category' => 'required|string|in:Laptop,Computer,Peripherals,Ergonomics',
            'cost' => 'required|integer|min:0',
            'condition' => 'required|string|in:new,used,broken',
            'acquisition_date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'storage_location' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'brand.required' => 'The brand is required.',
            'model.required' => 'The model is required.',
            'category.required' => 'The category is required.',
            'category.in' => 'The category must be one of: Laptop, Computer, Peripherals, Ergonomics.',
            'cost.required' => 'The cost is required.',
            'cost.integer' => 'The cost must be an integer.',
            'condition.required' => 'The condition is required.',
            'condition.in' => 'The condition must be one of: new, used, broken.',
            'acquisition_date.required' => 'The acquisition date is required.',
            'acquisition_date.date_format' => 'The acquisition date must be in YYYY-MM-DD format.',
            'acquisition_date.before_or_equal' => 'The acquisition date cannot be in the future.',
            'storage_location.required' => 'The storage location is required.',
        ];
    }
}
