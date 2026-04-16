<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class LoanEquipmentRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'loan_date' => 'required|date_format:Y-m-d|today_or_after',
            'loan_expire_date' => 'required|date_format:Y-m-d|after:loan_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'A user must be selected to loan the equipment.',
            'user_id.exists' => 'The selected user does not exist.',
            'loan_date.required' => 'The loan date is required.',
            'loan_date.date_format' => 'The loan date must be in YYYY-MM-DD format.',
            'loan_date.today_or_after' => 'The loan date must be today or in the future.',
            'loan_expire_date.required' => 'The loan expiration date is required.',
            'loan_expire_date.date_format' => 'The loan expiration date must be in YYYY-MM-DD format.',
            'loan_expire_date.after' => 'The loan expiration date must be after the loan date.',
        ];
    }
}

