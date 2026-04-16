<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'dob' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user->id,
            'role' => 'required|in:Manager,Employee',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The first name is required.',
            'surname.required' => 'The surname is required.',
            'dob.required' => 'The date of birth is required.',
            'email.required' => 'The email is required.',
            'email.unique' => 'This email already exists.',
            'role.required' => 'The role is required.',
            'role.in' => 'The role must be either Manager or Employee.',
        ];
    }
}
