<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class ReturnEquipmentRequest extends FormRequest
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
            'return_date' => 'required|date_format:Y-m-d|today_or_before',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'return_date.required' => 'The return date is required.',
            'return_date.date_format' => 'The return date must be in YYYY-MM-DD format.',
            'return_date.today_or_before' => 'The return date must be today or in the past.',
        ];
    }
}

